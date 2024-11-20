<?php
/*役割：投稿に関する処理を行う
主な処理：
・全ての投稿を取得する
・IDに基づいて特定の投稿を取得する
・投稿の編集を更新する
・投稿を削除する
・レスポンスを取得する（親IDに基づいて）
・投稿を作成する
*/

require_once '../db.php';


// 全ての投稿を取得する関数
function getAllPosts($page = 1, $per_page = 10)
{
    global $pdo;

    // ページネーションの計算
    $offset = ($page - 1) * $per_page;

    // 投稿一覧を取得
    $stmt = $pdo->prepare("SELECT p.id, p.created_at, p.title, COUNT(r.id) AS res_count, u.name 
                           FROM Posts p
                           LEFT JOIN Posts r ON r.parent_id = p.id
                           LEFT JOIN Users u ON p.user_id = u.id
                           WHERE p.parent_id IS NULL
                           GROUP BY p.id
                           ORDER BY p.created_at DESC
                           LIMIT :limit OFFSET :offset");
    $stmt->bindValue(':limit', $per_page, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    $posts = $stmt->fetchAll();
    return $posts;
}

function getTotalPages($per_page = 10)
{
    global $pdo;

    // 投稿数を取得
    $stmt = $pdo->query("SELECT COUNT(*) AS total FROM Posts WHERE parent_id IS NULL");
    $total_posts = $stmt->fetch()['total'];

    // 総ページ数を計算
    return ceil($total_posts / $per_page);
}

// IDに基づいて特定の投稿を取得する関数
function getPostById($id)
{
    global $pdo;
    $stmt = $pdo->prepare("SELECT Posts.id, Posts.title, Posts.content, Users.name, Posts.created_at 
                           FROM Posts 
                           JOIN Users ON Posts.user_id = Users.id 
                           WHERE Posts.id = ? AND Posts.is_deleted = 0");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

// 投稿の編集を更新する関数
function updatePost($id, $title, $content)
{
    global $pdo;
    $stmt = $pdo->prepare("UPDATE Posts SET title = ?, content = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ? AND is_deleted = 0");
    $stmt->execute([$title, $content, $id]);
    echo "投稿が更新されました！";
}

// POSTリクエスト処理(更新)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    updatePost($_POST['id'], $_POST['title'], $_POST['content']);
    header('Location: ../Views/post_list.php');
    exit;
}



// 投稿を削除する関数 (論理削除)
function deletePost($post_id)
{
    global $pdo;

    $stmt = $pdo->prepare("UPDATE Posts SET is_deleted = 1 WHERE id = ?");
    $stmt->execute([$post_id]);
}

// POSTリクエスト処理(削除)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    deletePost($_POST['id']);
    header('Location: ../Views/post_list.php'); // 修正
    exit;
}



// レスポンスを取得する関数(親IDに基づいて)
function getResponses($parent_id)
{
    global $pdo;

    $stmt = $pdo->prepare("SELECT * FROM Posts WHERE parent_id = ? AND is_deleted = 0 ORDER BY created_at ASC");
    $stmt->execute([$parent_id]);
    return $stmt->fetchAll();

}


function createPost($user_id, $title, $content) {
    global $pdo;

    $stmt = $pdo->prepare("INSERT INTO Posts (user_id, title, content, created_at, updated_at) VALUES (?, ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)");
    $stmt->execute([$user_id, $title, $content]);
}

// POSTリクエスト処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
    session_start();

    // ログイン状態の確認
    if (!isset($_SESSION['user_id'])) {
        header('Location: ../Views/login.php');
        exit;
    }

    // データを受け取り、投稿作成
    $user_id = $_SESSION['user_id'];
    $title = $_POST['title'];
    $content = $_POST['content'];

    createPost($user_id, $title, $content);

    // 投稿一覧ページにリダイレクト
    header('Location: ../Views/post_list.php');
    exit;
}


// レスポンス投稿処理
function createResponse($user_id, $parent_id, $content) {
    global $pdo;

    // 親投稿に既にレスポンスがあるか確認
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM Posts WHERE parent_id = ? AND is_deleted = 0");
    $stmt->execute([$parent_id]);
    $response_count = $stmt->fetchColumn();

    // レスポンスが既に1件ある場合、投稿を中断
    if ($response_count >= 1) {
        echo "レスポンスは1件までしか投稿できません。";
        exit;
    }

    // レスポンスを新規作成
    $stmt = $pdo->prepare("INSERT INTO Posts (user_id, parent_id, content, created_at, updated_at) VALUES (?, ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)");
    $stmt->execute([$user_id, $parent_id, $content]);
}

// POSTリクエスト処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    session_start();

    // ログイン状態の確認
    if (!isset($_SESSION['user_id'])) {
        header('Location: ../Views/login.php');
        exit;
    }

    if ($_POST['action'] === 'response') {
        // レスポンス投稿処理
        $user_id = $_SESSION['user_id'];
        $parent_id = (int)$_POST['parent_id'];
        $content = $_POST['content'];

        createResponse($user_id, $parent_id, $content);

        // 投稿詳細ページにリダイレクト
        header('Location: ../Views/post_detail.php?id=' . $parent_id);
        exit;
    }
}
