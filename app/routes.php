<?php
declare(strict_types=1);

use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use App\Application\Database\FileDataGateway;
use App\Application\Database\Connection;

return function (App $app) {
    $loader = new FilesystemLoader('..\templates');
    $twig = new Environment($loader);
    $gateway = new FileDataGateway(new Connection());

    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        return $response;
    });

    /**
     * Вывод страницы с загрузкой файлов
     */
    $app->get('/', function (Request $request, Response $response) use ($twig) {
        $body = $twig->render('index.twig');
        $response->getBody()->write($body);
        return $response;
    });

    /**
     * Вывод страницы со списком файлов
     */
    $app->get('/list', function (Request $request, Response $response, $args = []) use ($twig, $gateway) {
        //Настройки пагинации
        if(isset($request->getQueryParams()['page'])){
            $page = ($request->getQueryParams()['page'] > 0) ? $request->getQueryParams()['page'] : 1;
        } else {
            $page = 1;
        }
        $limit = 15; // Количество постов на 1 странице
        $skip = ($page - 1) * $limit; //Вычисление отступа для выборки
        $count = $gateway->getCount();  // Общее количество постов
        $files = $gateway->getAllWithPaginate($skip, $limit);

        $body = $twig->render('files_list.twig', [
            'files' => $files,
            'pagination' => [
                'needed'        => $count > $limit,
                'count'         => $count,
                'page'          => $page,
                'lastpage'      => (ceil($count / $limit) == 0 ? 1 : ceil($count / $limit)),
                'limit'         => $limit,
            ]
        ]);
        $response->getBody()->write($body);
        return $response;
    });

    /**
     * Вывод страницы с конкретным файлом
     */
    $app->get('/file/{name}', function (Request $request, Response $response, $args) use ($twig, $gateway) {
        try {
            $file = $gateway->getFileByName($args['name']);
            $path = '/storage/' . $file['filename'] . '.' . $file['format'];
            $body = $twig->render('file_page.twig', ['path' => $path, 'file' => $file]);
            $response->getBody()->write($body);
            return $response;
        } catch(Exception $exception){
            return $response->withStatus(404);
        }
    });

    /**
     * Загрузка файла на сервер
     */
    $app->post('/store', function (Request $request, Response $response) use ($gateway) {
        try{
            $directory = $this->get('storage');
            $files = $request->getUploadedFiles();
            $file = $files['file'];
            $ext = pathinfo($file->getClientFilename(), PATHINFO_EXTENSION);
            $basename =  mt_rand(10000000, 99999999);
            $file->moveTo($directory . DIRECTORY_SEPARATOR . $basename.'.'.$ext);
            $path = $gateway->insert(strval($basename), $ext, $file->getSize());
        } catch(Exception $exception){
            return $response->withStatus(404);
        }
        return $response->withHeader('Location', '/file/' . $path['filename'])->withStatus(303);
    });

    $app->get('/register', function (Request $request, Response $response) {
        $response->getBody()->write('Registration page!');
        return $response;
    });

    $app->group('/users', function (Group $group) {
        $group->get('', ListUsersAction::class);
        $group->get('/{id}', ViewUserAction::class);
    });
};
