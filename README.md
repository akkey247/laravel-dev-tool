# インストール

## 1. ダウンロード

Laravelの環境内にパッケージディレクトリを作成し、リポジトリをクローンする。  

```
$ mkdir -p packages/akkey247
$ cd packages/akkey247
$ gh repo clone akkey247/laravel-dev-tool
```

## 2. 設定ファイル修正

Laravel本体のcomposer.jsonに下記を追加する。  

```
    },
    "repositories": [{
        "type": "path",
        "url": "./packages/akkey247/laravel-dev-tool",
        "options": {
            "symlink": true
        }
    }],
    "autoload": {
```

## 3. インストール

Laravel本体のルートディレクトリで以下のコマンドを実行する。  

```
$ composer require akkey247/laravel-dev-tool:dev-main
```

## 4. 確認

Laravel本体のルートディレクトリで以下のように自作のartisanコマンドが存在しているか確認する。  

```
$ php artisan list | grep make:model-crud
```

# 使い方
