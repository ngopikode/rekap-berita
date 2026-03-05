<?php

namespace App\Helpers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Random\RandomException;
use Throwable;

/**
 * Class Helpers
 *
 * General static helper methods for the application.
 *
 * @package App\Helpers
 */
class TelegramHelper
{
    /**
     * Send exception or error report to Telegram channel via static call.
     *
     * @param mixed $errors The exception object, array, or string.
     * @param string $message Custom error message context.
     * @param int $code HTTP Status code or Error code.
     * @return string The generated Request ID.
     * @throws RandomException
     */
    public static function reportToTelegram(
        mixed   $errors,
        Request $request,
        string  $message = 'System Error',
        int     $code = 500
    ): string
    {
        $requestId = 'ERR-' . Carbon::now()->format('YmdHis') . '-' . bin2hex(random_bytes(4));

        try {
            $request = $request ?? request();
            $user = $request->user();

            $userInfo = $user ? "ID: {$user->id} ({$user->name})" : "Guest";
            $method = $request->method();
            $url = $request->fullUrl();
            $ip = $request->ip();

            $fileInfo = "";

            if ($errors instanceof Throwable) {
                $errorDetail = $errors->getMessage();
                $fileInfo = "\n📂 <b>File:</b> " . basename($errors->getFile()) . ":{$errors->getLine()}";
            } elseif (is_array($errors) || is_object($errors)) {
                $errorDetail = json_encode($errors);
            } else {
                $errorDetail = (string)$errors;
            }

            $text = "🚨 <b>CRITICAL ERROR REPORT</b> 🚨\n\n" .
                "🆔 <b>Req ID:</b> <code>{$requestId}</code>\n" .
                "👤 <b>User:</b> {$userInfo}\n" .
                "🌐 <b>IP:</b> {$ip}\n" .
                "🔗 <b>Method:</b> {$method} {$url}\n" .
                "🔢 <b>Code:</b> {$code}\n" .
                "----------------------------\n" .
                "💬 <b>Message:</b> {$message}\n" .
                "💥 <b>Exception:</b> {$errorDetail}" .
                $fileInfo;

            $token = config('services.telegram.bot_token');
            $chatId = config('services.telegram.chat_id');

            if ($token && $chatId) {
                Http::timeout(3)->post("https://api.telegram.org/bot{$token}/sendMessage", [
                    'chat_id' => $chatId,
                    'text' => substr($text, 0, 4090),
                    'parse_mode' => 'HTML',
                    'disable_web_page_preview' => true
                ]);
            }
        } catch (Throwable $e) {
            Log::error("Failed to report to Telegram: " . $e->getMessage());
        }

        return $requestId;
    }
}
