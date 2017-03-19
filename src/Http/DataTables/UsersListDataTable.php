<?php namespace WebEd\Base\Users\Http\DataTables;

use Illuminate\Database\Eloquent\SoftDeletes;
use WebEd\Base\Http\DataTables\AbstractDataTables;
use WebEd\Base\Users\Models\User;
use WebEd\Base\Users\Repositories\Contracts\UserRepositoryContract;
use WebEd\Base\Users\Repositories\UserRepository;

class UsersListDataTable extends AbstractDataTables
{
    /**
     * @var User
     */
    protected $model;

    /**
     * @var UserRepository
     */
    protected $repository;

    /**
     * @var array|\Illuminate\Http\Request|string
     */
    protected $request;

    public function __construct(UserRepositoryContract $repository)
    {
        $this->model = User::select('id', 'created_at', 'avatar', 'username', 'email', 'status', 'sex', 'deleted_at')
            ->withTrashed();

        $this->repository = $repository;

        parent::__construct();

        $this->request = request();
    }

    /**
     * @return string
     */
    public function run()
    {
        $this->setAjaxUrl(route('admin::users.index.post'), 'POST');

        $this
            ->addHeading('avatar', 'Avatar', '1%')
            ->addHeading('username', 'Username', '10%')
            ->addHeading('email', 'Email', '10%')
            ->addHeading('status', 'Status', '5%')
            ->addHeading('created_at', 'Created at', '5%')
            ->addHeading('roles', 'Roles', '15%')
            ->addHeading('actions', 'Actions', '15%');

        $this
            ->addFilter(2, form()->text('username', '', [
                'class' => 'form-control form-filter input-sm',
                'placeholder' => 'Search...'
            ]))
            ->addFilter(3, form()->email('email', '', [
                'class' => 'form-control form-filter input-sm',
                'placeholder' => 'Search...'
            ]))
            ->addFilter(4, form()->select('status', [
                '' => '',
                'activated' => 'Activated',
                'disabled' => 'Disabled',
                'deleted' => 'Deleted',
            ], '', ['class' => 'form-control form-filter input-sm']));

        $this->withGroupActions([
            '' => 'Select...',
            'activated' => 'Activated',
            'disabled' => 'Disabled',
            'deleted' => 'Deleted',
        ]);

        $this->setColumns([
            ['data' => 'id', 'name' => 'id', 'searchable' => false, 'orderable' => false],
            ['data' => 'avatar', 'name' => 'avatar', 'searchable' => false, 'orderable' => false],
            ['data' => 'username', 'name' => 'username'],
            ['data' => 'email', 'name' => 'email'],
            ['data' => 'status', 'name' => 'status'],
            ['data' => 'created_at', 'name' => 'created_at', 'searchable' => false],
            ['data' => 'roles', 'name' => 'roles', 'searchable' => false, 'orderable' => false],
            ['data' => 'actions', 'name' => 'actions', 'searchable' => false, 'orderable' => false],
        ]);

        return $this->view();
    }

    /**
     * @return $this
     */
    protected function fetch()
    {
        $this->fetch = datatable()->of($this->model)
            ->filterColumn('status', function ($query, $keyword) {
                /**
                 * @var UserRepository $query
                 */
                if ($keyword === 'deleted') {
                    return $query->onlyTrashed();
                } else {
                    return $query->where('status', '=', $keyword);
                }
            })
            ->editColumn('avatar', function ($item) {
                return '<img src="' . get_image($item->avatar) . '" width="50" height="50">';
            })
            ->editColumn('id', function ($item) {
                return form()->customCheckbox([['id[]', $item->id]]);
            })
            ->editColumn('status', function ($item) {
                /**
                 * @var SoftDeletes $item
                 */
                if ($item->trashed()) {
                    return html()->label('deleted', 'deleted');
                }
                return html()->label($item->status, $item->status);
            })
            ->addColumn('roles', function ($item) {
                $result = [];
                $roles = $this->repository->getRoles($item);
                if ($roles) {
                    foreach ($roles as $key => $row) {
                        $result[] = $row->name;
                    }
                }
                return implode(', ', $result);
            })
            ->addColumn('actions', function ($item) {
                /*Edit link*/
                $activeLink = route('admin::users.update-status.post', ['id' => $item->id, 'status' => 'activated']);
                $disableLink = route('admin::users.update-status.post', ['id' => $item->id, 'status' => 'disabled']);
                $deleteLink = route('admin::users.delete.delete', ['id' => $item->id]);
                $forceDelete = route('admin::users.force-delete.delete', ['id' => $item->id]);
                $restoreLink = route('admin::users.restore.post', ['id' => $item->id]);

                /*Buttons*/
                $editBtn = link_to(route('admin::users.edit.get', ['id' => $item->id]), 'Edit', ['class' => 'btn btn-outline green btn-sm']);
                $activeBtn = ($item->status != 'activated' && !$item->trashed()) ? form()->button('Active', [
                    'title' => 'Active this item',
                    'data-ajax' => $activeLink,
                    'data-method' => 'POST',
                    'data-toggle' => 'confirmation',
                    'class' => 'btn btn-outline blue btn-sm ajax-link',
                ]) : '';
                $disableBtn = ($item->status != 'disabled' && !$item->trashed()) ? form()->button('Disable', [
                    'title' => 'Disable this item',
                    'data-ajax' => $disableLink,
                    'data-method' => 'POST',
                    'data-toggle' => 'confirmation',
                    'class' => 'btn btn-outline yellow-lemon btn-sm ajax-link',
                ]) : '';
                $deleteBtn = (!$item->trashed())
                    ? form()->button('Delete', [
                        'title' => 'Delete this item',
                        'data-ajax' => $deleteLink,
                        'data-method' => 'DELETE',
                        'data-toggle' => 'confirmation',
                        'class' => 'btn btn-outline red-sunglo btn-sm ajax-link',
                    ])
                    : form()->button('Force delete', [
                        'title' => 'Force delete this item',
                        'data-ajax' => $forceDelete,
                        'data-method' => 'DELETE',
                        'data-toggle' => 'confirmation',
                        'class' => 'btn btn-outline red-sunglo btn-sm ajax-link',
                    ]) . form()->button('Restore', [
                        'title' => 'Restore this item',
                        'data-ajax' => $restoreLink,
                        'data-method' => 'POST',
                        'data-toggle' => 'confirmation',
                        'class' => 'btn btn-outline blue btn-sm ajax-link',
                    ]);

                $activeBtn = ($item->status != 'activated') ? $activeBtn : '';
                $disableBtn = ($item->status != 'disabled') ? $disableBtn : '';

                return $editBtn . $activeBtn . $disableBtn . $deleteBtn;
            });

        return $this;
    }
}
