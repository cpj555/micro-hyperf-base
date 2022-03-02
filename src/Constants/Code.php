<?php

declare(strict_types=1);
namespace Losingbattle\MicroBase\Constants;

use Hyperf\Constants\AbstractConstants;
use Hyperf\Constants\Annotation\Constants;

/**
 * @Constants
 */
class Code extends AbstractConstants
{
    /**
     * @Message("Server Error！")
     */
    public const SERVER_ERROR = 500;

    /**
     * @Message("Handle Not Found")
     */
    public const HANDLE_NOT_FOUND = 404;

    /**
     * @Message("Method Not Allowed")
     */
    public const METHOD_NOT_ALLOWED = 405;

    /**
     * @Message("调用错误")
     */
    public const PROVIDER_RESULT_ERROR = 440001;

    /**
     * @Message("业务异常")
     */
    public const ERROR_ABNORMAL = 410002;

    /**
     * @Message("调用服务接口异常")
     */
    public const ERROR_REQUEST_SERVICE = 440003;

    /**
     * @Message("请求成功")
     */
    public const SUCCESS = 0;

    /**
     * @Message("必填参数不能为空")
     */
    public const EMPTY_ERROR = 20000; //必填参数不能为空

    /**
     * @Message("签名校验失败")
     */
    public const SIGN_ERROR = 20001; //签名校验失败

    /**
     * @Message("参数错误")
     */
    public const ERROR_PARAMS = 20002;

    /**
     * @Message("请求已过期")
     */
    public const OVERDUE_ERROR = 20003; //请求已过期

    /**
     * @Message("用户不存在")
     */
    public const USER_ERROR = 20004; //用户不存在

    /**
     * @Message("系统操作失败")
     */
    public const SYSTEM_ERROR = 50001; //系统操作失败

    /**
     * @Message("请求频繁,请稍微再试")
     */
    public const ERROR_BUSY_BUSINESS = 50002; //请求频繁

    /**
     * @Message("code error")
     */
    public const CODE_ERROR = 60000; //代码错误(未实现、未继承父类方法)

    public const ALERT_ERROR = 70000; //系统主动告警

    public const RPC_BUSINESS_ERROR = 90000;
}
