<?php
namespace Sublime;

use think\facade\Log;

class Handle extends \think\exception\Handle
{
    protected $ignoreReport = [
        '\\think\\exception\\HttpException',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception $exception
     * @return void
     */
    public function report(\Exception $exception)
    {
        if (!$this->isIgnoreReport($exception)) {
            // 收集异常数据
            if (config('app.app_debug')) {
                $data = [
                    'file'    => $exception->getFile(),
                    'line'    => $exception->getLine(),
                    'message' => $this->getMessage($exception),
                    'code'    => $this->getCode($exception),
                ];
                $log = "[{$data['code']}]{$data['message']}[{$data['file']}:{$data['line']}]";
            } else {
                $data = [
                    'code'    => $this->getCode($exception),
                    'message' => $this->getMessage($exception),
                ];
                $log = "[{$data['code']}]{$data['message']}";
            }
            $report = sprintf("%s\n %s\n %s:%d", $data['code'], $this->getMessage($exception), $exception->getFile(), $exception->getLine());
            $no_ignore = true;
            Log::record($log, 'error');
        }else{
        	$data = [
                'code'    => $this->getCode($exception),
                'message' => $this->getMessage($exception),
            ];
            $log = "[{$data['code']}]{$data['message']}";
        	Log::record($log, 'error');
        }
    }

    public function render(\Exception $e)
    {
        if ($e instanceof HttpException) {
            $statusCode = $e->getStatusCode();
        }
        $no       = $this->getCode($e);
        $message  = $this->getMessage($e);
        $filename = $e->getFile();
        $lineno   = $e->getLine();
        Log::record("[PHP ERROR]：{$no}-{$message} found in {$filename}L{$lineno}", 'error');
        $ret = Ret::alert("[PHP ERROR]：{$no}-{$message} found in {$filename}L{$lineno}");
        trace($ret);
        // echo($ret);
        return Ret::alert("[PHP ERROR]：{$no}-{$message} found in {$filename}L{$lineno}");
    }

}
