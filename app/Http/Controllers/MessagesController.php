<?php

namespace App\Http\Controllers;

use App\Exceptions\GtSalvumException;
use App\Services\MessagesService;
use App\User;
use Illuminate\Http\Request;

class MessagesController extends Controller
{
    const DEFAULT_LIMIT=20;

    /** @var MessagesService */
    private $messagesService;

    /**
     * MessagesController constructor.
     * @param MessagesService $messagesService
     */
    public function __construct(MessagesService $messagesService)
    {
        $this->messagesService = $messagesService;
    }

    /**
     * @return array
     */
    public function index(Request $request) {
        /** @var User $user */
        $user = auth()->user();

        $offset = (int) $request->get('offset', 0);
        $limit = (int) $request->get('limit', self::DEFAULT_LIMIT );
        $data = $this->messagesService->getAccessibleMessages($user, $offset, $limit );
        return $data;
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws \App\Exceptions\GtSalvumValidateException
     */
    public function create(Request $request) {
        /** @var User $user */
        $user = auth()->user();
        $data = $request->all();
        $rez = $this->messagesService->createMessage($data, $user);
        return $rez;
    }

    /**
     * @param Request $request
     * @param $id
     * @return bool
     * @throws \App\Exceptions\GtSalvumValidateException
     */
    public function update(Request $request, $id ) {
        /** @var User $user */
        $user = auth()->user();
        $data = $request->all();
        $rez = $this->messagesService->updateMessage($data, $id, $user);
        return $rez;
    }

    /**
     * @param $id
     * @return bool
     * @throws \App\Exceptions\GtSalvumValidateException
     * @throws GtSalvumException
     */
    public function delete( $id ) {
        /** @var User $user */
        $user = auth()->user();

        $rez = $this->messagesService->deleteMessage($id, $user);
        return $rez;
    }

    /**
     * @param $id
     * @return array
     * @throws \App\Exceptions\GtSalvumValidateException
     */
    public function view( $id ) {
        /** @var User $user */
        $user = auth()->user();

        $rez = $this->messagesService->viewMessage($id, $user);
        return $rez;
    }

    /**
     * @param $id
     * @return array
     * @throws \App\Exceptions\GtSalvumValidateException
     */
    public function viewLog ($id ) {
        /** @var User $user */
        $user = auth()->user();

        $rez = $this->messagesService->viewMessageLog($id, $user);
        return $rez;
    }
}
