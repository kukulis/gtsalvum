<?php
/**
 * Created by PhpStorm.
 * User: giedrius
 * Date: 21.3.23
 * Time: 01.31
 */

namespace App\Services;


use App\Exceptions\GtSalvumValidateException;
use App\Message;
use App\Task;
use App\Transformers\MessageTransformer;
use App\User;
use App\ViewLog;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

class MessagesService
{
    /** @var TaskRepository */
    private $taskRepository;

    /** @var MessagesRepository */
    private $messagesRepository;

    /**
     * MessagesService constructor.
     * @param TaskRepository $taskRepository
     * @param MessagesRepository $messagesRepository
     */
    public function __construct(TaskRepository $taskRepository, MessagesRepository $messagesRepository)
    {
        $this->taskRepository = $taskRepository;
        $this->messagesRepository = $messagesRepository;
    }


    /**
     * @param User $user
     * @param int $offset
     * @param int $limit
     * @return array
     */
    public function getAccessibleMessages(User $user, $offset, $limit ) {
        $messages = $this->messagesRepository->getUserMessages($user->id, $offset, $limit );
        $resource = new Collection($messages, new MessageTransformer());
        $fractal = new Manager();
        $array = $fractal->createData($resource)->toArray();
        return $array;
    }


    /**
     * @param $data
     * @param User $user
     * @return mixed
     * @throws GtSalvumValidateException
     */
    public function createMessage($data, User $user) {
        $mt = new MessageTransformer();
        $message=$mt->toMessage($data, new Message());
        $message->owner_id = $user->id;

        // validate fields
        $errors = $this->validateMessage($message);
        if ( count($errors) > 0 ) {
            $exception = new GtSalvumValidateException('Invalid message data' );
            $exception->setErrorMessages($errors);
            throw $exception;
        }

        /** @var Task $task */
        $task = Task::find( $message->task_id );
        if ( $task == null ) {
            throw new GtSalvumValidateException('Invalid task_id value given - no task with id ['.$message->task_id.']' );
        }

        // validate permissions
        $canCreate = false;

        // 1) owner of the task
        if ( $task->owner_id == $user->id ) {
            $canCreate = true;
        }

        // 2) assigned to the task
        if ( !$canCreate ) {
            $assignedUser = $this->taskRepository->findAssignedUser($task, $user->id);
            if ($assignedUser != null) {
                $canCreate = true;
            }
        }

        if ( !$canCreate ) {
            throw new GtSalvumValidateException('You are not allowed to create message for task ['.$message->task_id.']');
        }

        $message->save();
        return $message->id;
    }

    /**
     * @param $data
     * @param $id
     * @param User $user
     * @return bool
     * @throws GtSalvumValidateException
     */
    public function updateMessage($data, $id, User $user) {

        /** @var Message $message */
        $message = Message::find($id);

        if ( $message == null ) {
            throw new GtSalvumValidateException('There is no message with id ['.$id.']' );
        }

        // validate permissions
        if ( $message->owner_id != $user->id ) {
            throw new GtSalvumValidateException('You are not allowed to update message ['.$id.'] because you are not an owner of it' );
        }


        $mt = new MessageTransformer();
        $message=$mt->toMessage($data, $message);

        // validate fields
        $errors = $this->validateMessage($message);
        if ( count($errors) > 0 ) {
            $exception = new GtSalvumValidateException('Invalid message data' );
            $exception->setErrorMessages($errors);
            throw $exception;
        }

        $message->save();
        return true;
    }

    /**
     * @param int $id
     * @param User $user
     * @return bool
     * @throws GtSalvumValidateException
     */
    public function deleteMessage($id, User $user ) {
        $message = Message::find($id);
        if ( $message == null ) {
            throw new GtSalvumValidateException('There is no message with id ['.$id.']' );
        }

        // validate permissions
        if ( $message->owner_id != $user->id ) {
            throw new GtSalvumValidateException('You are not allowed to update message ['.$id.'] because you are not an owner of it' );
        }

        return true;
    }

    /**
     * @param $id
     * @param User $user
     * @return array
     * @throws GtSalvumValidateException
     */
    public function viewMessage ( $id, User $user ) {
        /** @var Message $message */
        $message = Message::find($id);
        if ( $message == null ) {
            throw new GtSalvumValidateException('There is no message with id ['.$id.']' );
        }

        // validate permissions
        $canview = false;
        // 1) owner of the message
        if ( $message->owner_id == $user->id ) {
            $canview = true;
        }

        /** @var Task $task */
        $task = Task::find( $message->task_id );
        // 2) owner of the task
        if ( !$canview ) {
            if ( $task->owner_id == $user->id ) {
                $canview = true;
            }
        }

        // 3) assigned to the task
        if ( !$canview ) {
            $assignedUser = $this->taskRepository->findAssignedUser($task, $user->id);
            if ($assignedUser != null) {
                $canview = true;
            }
        }

        if ( !$canview) {
            throw new GtSalvumValidateException('You are not allowed to view message ['.$id.']');
        }

        // log view event
        $viewLogs = ViewLog::query()
            ->where('user_id', '=', $user->id)
            ->where('message_id', '=', $message->id )->get();
        if ( count($viewLogs) > 0 ) {
            $viewLog = $viewLogs->shift();
        } else {
            $viewLog = new ViewLog();
            $viewLog->user_id = $user->id;
            $viewLog->message_id = $message->id;
            $viewLog->save();
        }

        $message->viewDate = $viewLog->updated_at;


        $resource = new Item($message, new MessageTransformer());

        $fractal = new Manager();
        $array = $fractal->createData($resource)->toArray();
        return $array;
    }

    /**
     * @param Message $message
     * @return array
     */
    public function validateMessage(Message $message) {
        $errors = [];

        if ( empty($message->subject)) {
            $errors[] = 'Message must have non empty Subject';
        }

        if ( strlen($message->subject) > Message::SUBJECT_LENGTH ) {
            $errors[] = 'Subject length should not exceed '.Message::SUBJECT_LENGTH;
        }

        if (empty($message->message)) {
            $errors[] = 'Message should not be empty';
        }

        if ( strlen($message->message) > Message::MESSAGE_LENGTH ) {
            $errors[] = 'Message length should not exceed '.Message::MESSAGE_LENGTH;
        }

        if (empty($message->task_id)) {
            $errors[] = 'Field task_id should be given';
        }
        return $errors;
    }
}