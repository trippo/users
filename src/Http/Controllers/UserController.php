<?php namespace WebEd\Base\Users\Http\Controllers;

use WebEd\Base\ACL\Repositories\Contracts\RoleContract;
use WebEd\Base\Core\Http\Controllers\BaseAdminController;
use WebEd\Base\Users\Http\DataTables\UsersListDataTable;
use WebEd\Base\Users\Http\Requests\AssignRolesRequest;
use WebEd\Base\Users\Repositories\Contracts\UserContract;
use Yajra\Datatables\Engines\BaseEngine;

class UserController extends BaseAdminController
{
    protected $module = 'webed-users';

    /**
     * @var \WebEd\Base\Users\Repositories\UserRepository
     */
    protected $repository;

    public function __construct(UserContract $userRepository)
    {
        parent::__construct();

        $this->repository = $userRepository;
        $this->breadcrumbs->addLink('Users', route('admin::users.index.get'));

        $this->getDashboardMenu($this->module);
    }

    public function getIndex(UsersListDataTable $usersListDataTable)
    {
        $this->setPageTitle('All users');

        $this->dis['dataTable'] = $usersListDataTable->run();

        return do_filter('users.index.get', $this)->viewAdmin('index');
    }

    /**
     * Get data for DataTable
     * @param UsersListDataTable|BaseEngine $usersListDataTable
     * @return \Illuminate\Http\JsonResponse
     */
    public function postListing(UsersListDataTable $usersListDataTable)
    {
        $data = $usersListDataTable->with($this->groupAction());

        return do_filter('datatables.users.index.post', $data, $this);
    }

    /**
     * Handle group actions
     * @return array
     */
    private function groupAction()
    {
        $data = [];
        if ($this->request->get('customActionType', null) == 'group_action') {
            if(!$this->repository->hasPermission($this->loggedInUser, 'edit-other-users')) {
                return [
                    'customActionMessage' => 'You do not have permission',
                    'customActionStatus' => 'danger',
                ];
            }

            $ids = collect($this->request->get('id', []))->filter(function ($value, $index) {
                return (int)$value !== (int)$this->loggedInUser->id;
            })->toArray();

            $actionValue = $this->request->get('customActionValue', 'activated');

            $result = $this->repository->updateMultiple($ids, [
                'status' => $actionValue,
            ], true);

            $data['customActionMessage'] = $result['messages'];
            $data['customActionStatus'] = $result['error'] ? 'danger' : 'success';
        }
        return $data;
    }

    /**
     * Update page status
     * @param $id
     * @param $status
     * @return \Illuminate\Http\JsonResponse
     */
    public function postUpdateStatus($id, $status)
    {
        $data = [
            'status' => $status
        ];

        if (auth()->user()->id == $id) {
            $result = $this->repository->setMessages('You cannot update status of yourself', true, 500);
        } else {
            $result = $this->repository->updateUser($id, $data);
        }
        return response()->json($result, $result['response_code']);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getCreate()
    {
        $this->setPageTitle('Create user');
        $this->breadcrumbs->addLink('Create user');

        $this->dis['isLoggedInUser'] = false;
        $this->dis['isSuperAdmin'] = $this->loggedInUser->isSuperAdmin();

        $this->dis['currentId'] = 0;

        $this->dis['object'] = $this->repository->getModel();

        $oldInputs = old();
        if ($oldInputs) {
            foreach ($oldInputs as $key => $row) {
                $this->dis['object']->$key = $row;
            }
        }

        $this->assets
            ->addStylesheets('bootstrap-datepicker')
            ->addJavascripts('bootstrap-datepicker')
            ->addJavascriptsDirectly(asset('admin/modules/users/user-profiles/user-profiles.js'))
            ->addStylesheetsDirectly(asset('admin/modules/users/user-profiles/user-profiles.css'));

        return do_filter('users.create.get', $this)->viewAdmin('create');
    }

    /**
     * @param \WebEd\Base\ACL\Repositories\RoleRepository $roleRepository
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getEdit(RoleContract $roleRepository, $id)
    {
        $this->dis['isLoggedInUser'] = (int)$this->loggedInUser->id === (int)$id ? true : false;
        $this->dis['isSuperAdmin'] = $this->loggedInUser->isSuperAdmin();

        if ((int)$this->loggedInUser->id !== (int)$id) {
            if(!$this->repository->hasPermission($this->loggedInUser, 'edit-other-users')) {
                return redirect()->to(route('admin::error', ['code' => 403]));
            }
        }

        $item = $this->repository->find($id);

        if (!$item) {
            $this->flashMessagesHelper
                ->addMessages('User not found', 'danger')
                ->showMessagesOnSession();

            return redirect()->back();
        }

        $this->setPageTitle('Edit user', '#' . $id);
        $this->breadcrumbs->addLink('Edit user');

        $this->dis['object'] = $item;

        if (!$this->dis['isLoggedInUser'] && ($this->dis['isSuperAdmin'] || $this->repository->hasPermission($this->loggedInUser, 'assign-roles'))) {
            $roles = $roleRepository->all();

            $checkedRoles = $item->roles()->getRelatedIds()->toArray();

            $resolvedRoles = [];
            foreach ($roles as $role) {
                $resolvedRoles[] = [
                    'roles[]', $role->id, $role->name, (in_array($role->id, $checkedRoles))
                ];
            }
            $this->dis['roles'] = $resolvedRoles;
        }

        $this->dis['currentId'] = $id;

        $this->assets
            ->addStylesheets('bootstrap-datepicker')
            ->addJavascripts('bootstrap-datepicker')
            ->addJavascriptsDirectly(asset('admin/modules/users/user-profiles/user-profiles.js'))
            ->addStylesheetsDirectly(asset('admin/modules/users/user-profiles/user-profiles.css'));

        return do_filter('users.edit.get', $this, $id)->viewAdmin('edit');
    }

    /**
     * Create/Edit page
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postEdit(AssignRolesRequest $assignRolesRequest, $id)
    {
        if ((int)$this->loggedInUser->id !== (int)$id) {
            if(!$this->repository->hasPermission($this->loggedInUser, 'edit-other-users')) {
                return redirect()->to(route('admin::error', ['code' => 403]));
            }
        }
        if ($this->request->exists('roles')) {
            if(!$this->repository->hasPermission($this->loggedInUser, 'assign-roles')) {
                return redirect()->to(route('admin::error', ['code' => 403]));
            }
        }

        $data = [];
        if ($assignRolesRequest->requestHasRoles()) {
            $data['roles'] = $assignRolesRequest->getResolvedRoles();
        } else {
            if ($this->request->get('_tab') === 'roles') {
                $data['roles'] = [];
            }
        }
        if ($this->request->exists('birthday') && !$this->request->get('birthday')) {
            $data['birthday'] = null;
        }

        if (!$id) {
            $result = $this->createUser($data);
        } else {
            $result = $this->updateUser($id, $data);
        }

        $msgType = $result['error'] ? 'danger' : 'success';

        $this->flashMessagesHelper
            ->addMessages($result['messages'], $msgType)
            ->showMessagesOnSession();

        if ($result['error']) {
            if (!$id) {
                return redirect()->back()->withInput();
            }
        }

        do_action('users.after-edit.post', $id, $result, $this);

        if ($this->request->has('_continue_edit')) {
            if (!$id) {
                return redirect()->to(route('admin::users.edit.get', ['id' => $result['data']->id]));
            }
            return redirect()->back();
        }

        return redirect()->to(route('admin::users.index.get'));
    }

    /**
     * @param array $crossData
     * @return array|mixed
     */
    private function createUser(array $crossData)
    {
        if(!$this->repository->hasPermission($this->loggedInUser, 'create-users')) {
            return redirect()->to(route('admin::error', ['code' => 403]));
        }

        $data = array_merge($this->request->except([
            '_token', '_continue_edit', '_tab', 'roles',
        ]), $crossData);

        $data['created_by'] = $this->loggedInUser->id;
        $data['updated_by'] = $this->loggedInUser->id;

        return $this->repository->createUser($data);
    }

    /**
     * @param $id
     * @param array $crossData
     * @return array|mixed
     */
    private function updateUser($id, array $crossData)
    {
        $data = array_merge($this->request->except([
            '_token', '_continue_edit', '_tab', 'username', 'email', 'roles'
        ]), $crossData);

        /**
         * It's shit if current user can edit their roles
         */
        $isLoggedInUser = (int)$this->loggedInUser->id === (int)$id ? true : false;
        if ($isLoggedInUser) {
            if ($this->request->exists('roles')) {
                unset($data['roles']);
            }
        }

        $data['updated_by'] = $this->loggedInUser->id;

        return $this->repository->updateUser($id, $data);
    }
}
