<?php namespace WebEd\Base\Users\Http\Controllers\Front;

use WebEd\Base\Http\Controllers\BaseFrontController;
use WebEd\Base\Users\Http\Requests\ForgotPasswordRequest;
use WebEd\Base\Users\ServerActions\ForgotPasswordServerAction;

class ForgotPasswordController extends BaseFrontController
{
    public function getIndex()
    {
        $this->setBodyClass('forgot-password-page');
        $this->setPageTitle(trans('webed-users::auth.forgot_password'));

        return $this->view(config('webed-auth.front_actions.forgot_password.view') ?: 'webed-users::front.auth.forgot-password');
    }

    public function postIndex(ForgotPasswordRequest $request, ForgotPasswordServerAction $action)
    {
        $result = $action->run($request);

        if ($result['error']) {
            flash_messages()
                ->addMessages($result['messages'], 'error')
                ->showMessagesOnSession();
            return redirect()->back();
        }

        flash_messages()
            ->addMessages($result['messages'], 'success')
            ->showMessagesOnSession();

        return redirect()->to(route('front.web.resolve-pages.get'));
    }
}
