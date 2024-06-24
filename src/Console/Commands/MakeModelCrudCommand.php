<?php

namespace Akkey247\LaravelDevTool\Console\Commands;

use Illuminate\Console\Command;

class MakeModelCrudCommand extends Command
{
    /**
     * コマンド名と引数、オプションの定義
     *
     * @var string
     */
    protected $signature = 'make:model-crud {name} {targets?*}';

    /**
     * コマンドの説明
     *
     * @var string
     */
    protected $description = 'モデルのCRUD機能一式を作成';

    /**
     * スタブディレクトリのパス
     *
     * @var string
     */
    protected $stubDirPath = __DIR__ . '/../../../resources/stubs/make_model_crud_command/';

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
            if (!$this->createMigration($name)) {
                $this->error('Failed to create migration.');
                return;
            }
            $this->info('Migration created successfully.');
        }

        // モデル作成
        if (in_array('model', $targets)) {
            if (!$this->createModel($name)) {
                $this->error('Failed to create model.');
                return;
            }
            $this->info('Model created successfully.');
        }

        // コントローラー作成
        if (in_array('controller', $targets)) {
            if (!$this->createController($name)) {
                $this->error('Failed to create controller.');
                return;
            }
            $this->info('Controller created successfully.');
        }

        // ルート作成
        if (in_array('routes', $targets)) {
            if (!$this->createRoutes($name)) {
                $this->error('Failed to create routes.');
                return;
            }
            $this->info('Routes created successfully.');
        }

        // ビュー作成
        if (in_array('views', $targets)) {
            if (!$this->createView($name, 'list')) {
                $this->error('Failed to create \'list\' view.');
                return;
            }
            if (!$this->createView($name, 'detail')) {
                $this->error('Failed to create \'detail\' view.');
                return;
            }
            if (!$this->createView($name, 'create')) {
                $this->error('Failed to create \'create\' view.');
                return;
            }
            if (!$this->createView($name, 'edit')) {
                $this->error('Failed to create \'edit\' view.');
                return;
            }
            $this->info('Views created successfully.');
        }

        // リクエスト作成
        if (in_array('requests', $targets)) {
            if (!$this->createRequest($name, 'Store')) {
                $this->error('Failed to create \'store\' request.');
                return;
            }
            if (!$this->createRequest($name, 'Update')) {
                $this->error('Failed to create \'update\' request.');
                return;
            }
            if (!$this->createRequest($name, 'Destroy')) {
                $this->error('Failed to create \'destroy\' request.');
                return;
            }
            $this->info('Request created successfully.');
        }

        $this->info('All created successfully.');
    }

    /**
     * マイグレーション作成
     * 
     * @param string $name 名前
     * @return bool 作成成功したか
     */
    protected function createMigration(string $name): bool
    {
        $stub = file_get_contents($this->stubDirPath . 'migration.stub');
        $stub = $this->replaceStub($name, $stub);

        extract($this->getReplaceNames($name));

        $datetime = date('Y_m_d_His');
        $path = database_path("migrations/{$datetime}_create_{$model_names}_table.php");

        return $this->createFile($path, $stub);
    }

    /**
     * モデルクラス作成
     * 
     * @param string $name 名前
     * @return bool 作成成功したか
     */
    protected function createModel(string $name): bool
    {
        $stub = file_get_contents($this->stubDirPath . 'model.stub');
        $stub = $this->replaceStub($name, $stub);

        extract($this->getReplaceNames($name));

        $path = app_path("Models/{$ModelName}.php");

        return $this->createFile($path, $stub);
    }

    /**
     * コントローラークラス作成
     * 
     * @param string $name 名前
     * @return bool 作成成功したか
     */
    protected function createController(string $name): bool
    {
        $stub = file_get_contents($this->stubDirPath . 'controller.stub');
        $stub = $this->replaceStub($name, $stub);

        extract($this->getReplaceNames($name));

        $path = app_path("Http/Controllers/{$DirNameSlash}{$ModelName}Controller.php");

        return $this->createFile($path, $stub);
    }

    /**
     * ルート作成
     * 
     * @param string $name 名前
     * @return bool 作成成功したか
     */
    protected function createRoutes(string $name): bool
    {
        $stub = file_get_contents($this->stubDirPath . 'routes.stub');
        $stub = $this->replaceStub($name, $stub);

        extract($this->getReplaceNames($name));

        $path = base_path("routes/{$model_name}.php");

        return $this->createFile($path, $stub);
    }

    /**
     * ビュー作成
     * 
     * @param string $name 名前
     * @param string $view_name ビュー名
     * @return bool 作成成功したか
     */
    protected function createView(string $name, string $view_name): bool
    {
        $stub = file_get_contents($this->stubDirPath . "view_{$view_name}.stub");
        $stub = $this->replaceStub($name, $stub);

        extract($this->getReplaceNames($name));

        $path = resource_path("views/{$dir_name_slash}{$model_name}/{$view_name}.blade.php");

        return $this->createFile($path, $stub);
    }

    /**
     * リクエストクラス作成
     * 
     * @param string $name 名前
     * @param string $ViewName ビュー名
     * @return bool 作成成功したか
     */
    protected function createRequest(string $name, string $ViewName): bool
    {
        $stub = file_get_contents($this->stubDirPath . 'request.stub');
        $stub = $this->replaceStub($name, $stub);
        $stub = str_replace('{{ViewName}}', $ViewName, $stub);

        extract($this->getReplaceNames($name));

        $path = app_path("Http/Requests/{$ModelName}/{$ModelName}{$ViewName}Request.php");

        return $this->createFile($path, $stub);
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

    /**
     * スタブの置換
     * 
     * @param string $name 名前
     * @param string $stub 置換するスタブ
     * @return string 置換後のスタブ
     */
    function replaceStub(string $name, string $stub): string
    {
        extract($this->getReplaceNames($name));

        $stub = str_replace('{{BackSlashDirName}}', $BackSlashDirName, $stub);
        $stub = str_replace('{{dir_name_slash}}', $dir_name_slash, $stub);
        $stub = str_replace('{{dir_name_dot}}', $dir_name_dot, $stub);
        $stub = str_replace('{{ModelName}}', $ModelName, $stub);
        $stub = str_replace('{{modelName}}', $modelName, $stub);
        $stub = str_replace('{{modelNames}}', $modelNames, $stub);
        $stub = str_replace('{{model_name}}', $model_name, $stub);
        $stub = str_replace('{{model_names}}', $model_names, $stub);

        return $stub;
    }

    function createFile(string $path, string $stub): bool
    {
        if (file_exists($path)) {
            $this->error('File already exists!');
            return false;
        }

        $dirpath = dirname($path);
        if (!is_dir($dirpath)) {
            mkdir($dirpath, 0755, true);
        }
        file_put_contents($path, $stub);

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
