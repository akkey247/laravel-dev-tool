<?php

namespace Akkey247\LaravelDevTool\Console\Commands;

use Illuminate\Console\Command;

class PublishComponentsCommand extends Command
{
    /**
     * コマンド名と引数、オプションの定義
     *
     * @var string
     */
    protected $signature = 'publish:components {targets*}';

    /**
     * コマンドの説明
     *
     * @var string
     */
    protected $description = 'コンポーネントを公開';

    /**
     * コマンドのヘルプ情報を取得
     *
     * @return string
     */
    public function getHelp(): string
    {
        return <<<HELP
コンポーネントを公開するコマンドです。

使用方法:
  php artisan publish:components {targets*}

引数:
  targets    公開するコンポーネントの名前を指定します。複数指定可能です。
             指定可能な値: button, card, form など

使用例:
  - 複数のコンポーネントを公開:
    php artisan publish:components button card

  - 単一のコンポーネントを公開:
    php artisan publish:components form

公開されるファイル:
  - コンポーネントのビューファイル
  - コンポーネントのスタイルシート
  - コンポーネントのJavaScriptファイル
HELP;
    }

    /**
     * コマンド処理
     *
     * @return void
     */
    public function handle(): void
    {
        $this->info('Publishing components...');
    }
}
