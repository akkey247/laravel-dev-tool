<?php

namespace Akkey247\LaravelDevTool\Console\Commands;

use Illuminate\Console\Command;

class RemoveModelCrudCommand extends Command
{
    /**
     * コマンド名と引数、オプションの定義
     *
     * @var string
     */
    protected $signature = 'remove:model-crud {name} {targets?*}';

    /**
     * コマンドの説明
     *
     * @var string
     */
    protected $description = 'モデルのCRUD機能一式を削除';

    /**
     * コマンド処理
     *
     * @return void
     */
    public function handle(): void
    {
        $name = $this->argument('name');
        $targets = $this->argument('targets');
        $targetAllowed = ['migration', 'model', 'controller', 'routes', 'views', 'requests'];
        if (empty($targets)) {
            $targets = $targetAllowed;
        }
        if (array_diff($targets, $targetAllowed)) {
            $this->error('Invalid targets.');
            return;
        }

        // マイグレーション作成
        if (in_array('migration', $targets)) {
            if (!$this->removeMigration($name)) {
                $this->error('Failed to remove migration.');
                return;
            }
            $this->info('Migration removed successfully.');
        }

        // モデル作成
        if (in_array('model', $targets)) {
            if (!$this->removeModel($name)) {
                $this->error('Failed to remove model.');
                return;
            }
            $this->info('Model removed successfully.');
        }

        // コントローラー作成
        if (in_array('controller', $targets)) {
            if (!$this->removeController($name)) {
                $this->error('Failed to remove controller.');
                return;
            }
            $this->info('Controller removed successfully.');
        }

        // ルート作成
        if (in_array('routes', $targets)) {
            if (!$this->removeRoutes($name)) {
                $this->error('Failed to remove routes.');
                return;
            }
            $this->info('Routes removed successfully.');
        }

        // ビュー作成
        if (in_array('views', $targets)) {
            if (!$this->removeView($name, 'list')) {
                $this->error('Failed to remove \'list\' view.');
                return;
            }
            if (!$this->removeView($name, 'detail')) {
                $this->error('Failed to remove \'detail\' view.');
                return;
            }
            if (!$this->removeView($name, 'create')) {
                $this->error('Failed to remove \'create\' view.');
                return;
            }
            if (!$this->removeView($name, 'edit')) {
                $this->error('Failed to remove \'edit\' view.');
                return;
            }
            $this->info('Views removed successfully.');
        }

        // リクエスト作成
        if (in_array('requests', $targets)) {
            if (!$this->removeRequest($name, 'Store')) {
                $this->error('Failed to remove \'store\' request.');
                return;
            }
            if (!$this->removeRequest($name, 'Update')) {
                $this->error('Failed to remove \'update\' request.');
                return;
            }
            if (!$this->removeRequest($name, 'Destroy')) {
                $this->error('Failed to remove \'destroy\' request.');
                return;
            }
            $this->info('Request removed successfully.');
        }

        $this->info('All removed successfully.');
    }

    /**
     * マイグレーション作成
     * 
     * @param string $name 名前
     * @return bool 作成成功したか
     */
    protected function removeMigration(string $name): bool
    {
        extract($this->getReplaceNames($name));

        $path = database_path("migrations/*_create_{$model_names}_table.php");

        return $this->removeFile($path);
    }

    /**
     * モデルクラス作成
     * 
     * @param string $name 名前
     * @return bool 作成成功したか
     */
    protected function removeModel(string $name): bool
    {
        extract($this->getReplaceNames($name));

        $path = app_path("Models/{$ModelName}.php");

        return $this->removeFile($path);
    }

    /**
     * コントローラークラス作成
     * 
     * @param string $name 名前
     * @return bool 作成成功したか
     */
    protected function removeController(string $name): bool
    {
        extract($this->getReplaceNames($name));

        $path = app_path("Http/Controllers/{$DirNameSlash}{$ModelName}Controller.php");

        return $this->removeFile($path);
    }

    /**
     * ルート作成
     * 
     * @param string $name 名前
     * @return bool 作成成功したか
     */
    protected function removeRoutes(string $name): bool
    {
        extract($this->getReplaceNames($name));

        $path = base_path("routes/{$model_name}.php");

        return $this->removeFile($path);
    }

    /**
     * ビュー作成
     * 
     * @param string $name 名前
     * @param string $view_name ビュー名
     * @return bool 作成成功したか
     */
    protected function removeView(string $name, string $view_name): bool
    {
        extract($this->getReplaceNames($name));

        $path = resource_path("views/{$dir_name_slash}{$model_name}/{$view_name}.blade.php");

        return $this->removeFile($path);
    }

    /**
     * リクエストクラス作成
     * 
     * @param string $name 名前
     * @param string $ViewName ビュー名
     * @return bool 作成成功したか
     */
    protected function removeRequest(string $name, string $ViewName): bool
    {
        extract($this->getReplaceNames($name));

        $path = app_path("Http/Requests/{$ModelName}/{$ModelName}{$ViewName}Request.php");

        return $this->removeFile($path);
    }

    /**
     * 置換用の名前を取得
     * 
     * @param string $name 名前
     * @return array 置換用の名前
     */
    function getReplaceNames(string $name): array
    {
        list($dir, $name) = $this->splitByLastSlash($name);
        $DirName = $this->toPascalCase($dir);
        $dir_name = $this->toSnakeCase($dir);
        $ModelName = $this->toPascalCase($name);
        $modelName = $this->toCamelCase($name);
        $modelNames = $modelName . 's';
        $model_name = $this->toSnakeCase($name);
        $model_names = $model_name . 's';

        $DirNameSlash = ($DirName) ? $DirName . '/' : '';
        $BackSlashDirName = ($DirName) ? '\\' . $DirName : '';
        $dir_name_slash = ($dir_name) ? $dir_name . '/' : '';
        $dir_name_dot = ($dir_name) ? $dir_name . '.' : '';

        return compact(
            'DirName',
            'dir_name',
            'ModelName',
            'modelName',
            'modelNames',
            'model_name',
            'model_names',
            'DirNameSlash',
            'BackSlashDirName',
            'dir_name_slash',
            'dir_name_dot'
        );
    }

    function removeFile(string $path): bool
    {
        $paths = (strpos($path, '*') !== false) ? glob($path) : [$path];

        foreach ($paths as $path) {
            if (!file_exists($path)) {
                $this->warn('File already removed. Skip removing. [' . $path . ']');
                return true;
            }

            if (!unlink($path)) {
                $this->error('Failed to remove file.');
                return false;
            }

            $this->line('File removed. [' . $path . ']');
        }
        return true;
    }

    /**
     * 文字列を最後のスラッシュで分割
     * 
     * @param string $string 分割する文字列
     * @return array [スラッシュより前の文字列, スラッシュより後の文字列]
     */
    function splitByLastSlash(string $string): array
    {
        $lastSlashPosition = strrpos($string, '/');
        if ($lastSlashPosition === false) {
            return ['', $string];
        }
        $beforeLastSlash = substr($string, 0, $lastSlashPosition);
        $afterLastSlash = substr($string, $lastSlashPosition + 1);

        return [$beforeLastSlash, $afterLastSlash];
    }

    /**
     * 文字列をキャメルケースに変換
     * 
     * @param string $string 変換する文字列
     * @return string 変換後の文字列
     */
    function toCamelCase(string $string): string
    {
        $string = ucwords($string, "_");
        $string = str_replace("_", "", $string);
        return lcfirst($string);
    }

    /**
     * 文字列をパスカルケースに変換
     * 
     * @param string $string 変換する文字列
     * @return string 変換後の文字列
     */
    function toPascalCase(string $string): string
    {
        $string = ucwords($string, "_");
        return str_replace("_", "", $string);
    }

    /**
     * 文字列をスネークケースに変換
     * 
     * @param string $string 変換する文字列
     * @return string 変換後の文字列
     */
    function toSnakeCase(string $string): string
    {
        if (ctype_lower($string)) {
            return $string;
        }
        $string = preg_replace('/(?<!^)[A-Z]/', '_$0', $string);
        return strtolower($string);
    }
}
