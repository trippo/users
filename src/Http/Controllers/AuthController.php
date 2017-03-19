<?php namespace WebEd\Base\Users\Http\Controllers;

use WebEd\Base\Users\Http\Requests\AuthRequest;
use WebEd\Base\Users\Support\Traits\Auth;

use WebEd\Base\Http\Controllers\BaseController;
use WebEd\Base\Users\Repositories\Contracts\UserRepositoryContract;

class AuthController extends BaseController
{
    use Auth;

    /**
     * @var string
     */
    protected $module = 'webed-users';

    /**
     * @var string
     */
    public $username = 'username';

    /**
     * @var string
     */
    public $loginPath = 'auth';

    /**
     * @var string
     */
    public $redirectTo;

    /**
     * @var string
     */
    public $redirectPath;

    /**
     * @var string
     */
    public $redirectToLoginPage;

    /**
     * @var \WebEd\Base\AssetsManagement\Assets
     */
    protected $assets;

    /**
     * AuthController constructor.
     * @param \WebEd\Base\Users\Repositories\UserRepository $userRepository
     */
    public function __construct(UserRepositoryContract $userRepository)
    {
        $this->middleware('webed.guest-admin', ['except' => ['getLogout']]);

        parent::__construct();

        $this->repository = $userRepository;

        $this->redirectTo = route('admin::dashboard.index.get');
        $this->redirectPath = route('admin::dashboard.index.get');
        $this->redirectToLoginPage = route('admin::auth.login.get');

        $this->assets = \Assets::getAssetsFrom('admin');

        $this->assets
            ->addStylesheetsDirectly([
                'admin/theme/lte/css/AdminLTE.min.css',
                'admin/css/style.css',
            ])
            ->addJavascriptsDirectly([
                'admin/theme/lte/js/app.js',
                'admin/js/webed-core.js',
                'admin/js/script.js',
            ], 'bottom');
    }

    /**
     * Show login page
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getLogin()
    {
        $this->setBodyClass('login-page');
        $this->setPageTitle('Login');

        return $this->view('admin.auth.login');
    }

    /**
     * @param AuthRequest $authRequest
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function postLogin(AuthRequest $authRequest)
    {
        //Finish validate request

        return $this->login($this->request);
    }

    /**
     * Logout and redirect to login page
     * @return \Illuminate\Http\RedirectResponse
     */
    public function getLogout()
    {
        $this->guard()->logout();

        session()->flush();

        session()->regenerate();

        return redirect()->to($this->redirectToLoginPage);
    }
}
