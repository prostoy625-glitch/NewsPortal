<?php

namespace App\Controllers;
require_once __DIR__ . '/../Repositories/CategoryRepository.php';
require_once __DIR__ . '/../Repositories/NewsRepository.php';
require_once __DIR__ . '/../../core/View.php';

class HomeController extends Controller
{
    public function index(): void
    {
        $news = [];
        $categories = [];
        $databaseStatus = 'Подключение не проверено';

        // Получаем поисковый запрос
        $search = $_GET['search'] ?? '';
        $search = is_string($search) ? trim($search) : '';
        $search = mb_substr($search, 0, 100, 'UTF-8');

        // Получаем выбранную категорию
        $category = filter_input(
            INPUT_GET,
            'category',
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1]]
        );

        $categoryId = is_int($category) ? $category : null;

        // Получаем номер страницы
        $page = filter_input(
            INPUT_GET,
            'page',
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1]]
        );

        $page = is_int($page) ? $page : 1;

        $perPage = 5;
        $totalNews = 0;
        $totalPages = 0;
        $status = 200;

        try {
            // Создаём объекты репозиториев
            $repository = new \NewsRepository();
$categoryRepository = new \CategoryRepository();

            // Получаем категории
            $categories = $categoryRepository->getAll();

            // Подсчитываем опубликованные новости
            $totalNews = $repository->countPublished(
                $search,
                $categoryId
            );

            // Определяем количество страниц
            $totalPages = (int) ceil($totalNews / $perPage);

            // Проверяем номер страницы
            if ($totalPages > 0) {
                $page = min($page, $totalPages);
            } else {
                $page = 1;
            }

            // Получаем новости текущей страницы
            $news = $repository->getPublishedPaginated(
                $search,
                $categoryId,
                $page,
                $perPage
            );

            $databaseStatus = 'MySQL подключён успешно!';

        } catch (Throwable $e) {
            error_log($e->getMessage());

            $status = 500;

            $databaseStatus =
                'Хатогӣ ҳангоми гирифтани хабарҳо.';
        }

        // Отображаем страницу через базовый Controller
        $this->render('home', [
            'title' => 'NewsPortal',
            'databaseStatus' => $databaseStatus,
            'news' => $news,
            'search' => $search,
            'categoryId' => $categoryId,
            'page' => $page,
            'perPage' => $perPage,
            'totalNews' => $totalNews,
            'categories' => $categories,
            'totalPages' => $totalPages
        ], $status);
    }
}
