<?php
/*役割：投稿に関する処理を行う
主な処理：
1. 投稿一覧の取得
2. 投稿の作成
3. 投稿の更新
4. 投稿の削除
5. レスポンスの作成

*/
require_once __DIR__ . '/../db.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$param = array_merge($_GET, $_POST); // リクエストを統合

// ログイン状態の確認
if (!isset($_SESSION['user_id'])) {
    header('Location: ../Views/login.php');
    exit;
}

// アクションを取得
$action = $param['action'] ?? null;

// POSTリクエストの処理を統一
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    switch ($action) {
        case 'create':
            // 投稿作成時のバリデーション
            if (empty($_POST['title']) || empty($_POST['content'])) {
                echo "すべてのフォームを入力してください。";
                exit;
            }

            $user_id = $_SESSION['user_id'];
            $title = $_POST['title'];
            $content = $_POST['content'];
            createPost($user_id, $title, $content);
            header('Location: ../Views/post_list.php');

            // セッションに完了メッセージを保存 TODO ここって被ってるけど一緒にできないかな？
            $_SESSION['flash_message'] = "新規投稿が完了しました。";

            // 投稿一覧ページへリダイレクト
            header('Location: ../Views/post_list.php');
            exit;

        case 'update':
            // 投稿更新処理
            $post_id = (int)$_POST['id'];
            $title = $_POST['title'] ?? '';
            $content = $_POST['content'] ?? '';

            // 権限チェック
            $post = getPostById($post_id);
            if (!$post || (!isset($_SESSION['role'], $_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['user_id'] != $post['user_id']))) {
                echo "編集権限がありません。";
                exit;
            }

            // 投稿の更新
            updatePost($post_id, $title, $content);

            // セッションに完了メッセージを保存(javascriptでフェードアウト表示)
            $_SESSION['flash_message'] = "投稿の再編集が完了しました。";

            // 更新後、詳細ページへリダイレクト
            header('Location: ../Views/post_list.php');
            exit;

        case 'delete':
            // 削除処理専用バリデーション
            if (empty($_POST['id'])) {
                echo "削除対象の投稿が指定されていません。";
                exit;
            }
            $post_id = (int)$_POST['id'];
            $user_id = $_SESSION['user_id'];
            $is_admin = ($_SESSION['role'] === 'admin');
            deletePost($post_id, $user_id, $is_admin);
            //削除の確認を行う
            header('Location: ../Views/post_list.php');
            exit;

        case 'response':
            // レスポンス作成処理
            $user_id = $_SESSION['user_id'];
            $parent_id = (int)$_POST['parent_id'];
            $content = $_POST['content'] ?? '';
            createResponse($user_id, $parent_id, $content);
            header('Location: ../Views/post_detail.php?id=' . $parent_id);
            exit;

        default:
            echo "不正なリクエストです。";
            exit;
    }
}

// GETリクエストの処理（必要なら追加）
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!$action) {
        // GETリクエストにアクションが指定されていない場合、処理をスキップ
        return;
    }

    switch ($action) {
        case 'view':
            // 例: 投稿詳細表示（将来的な処理用）
            $post_id = (int)$_GET['id'];
            $post = getPostById($post_id);
            // ビューの表示処理...
            break;

        default:
            echo "無効なリクエストです。";
            exit;
    }
}



// 全ての投稿を取得する関数
function getAllPosts($page = 1, $per_page = 10)
{
    global $pdo;

    // ページネーションの計算
    $offset = ($page - 1) * $per_page;

    // 投稿一覧を取得(これは削除された投稿も含むので確認用)
    // $stmt = $pdo->prepare("SELECT p.id, p.created_at, p.title, COUNT(r.id) AS res_count, u.name FROM Posts pLEFT JOIN Posts r ON r.parent_id = p.idLEFT JOIN Users u ON p.user_id = u.idWHERE p.parent_id IS NULLGROUP BY p.idORDER BY p.created_at DESCLIMIT :limit OFFSET :offset");

    // 投稿一覧を取得（削除された投稿は除外）
    $stmt = $pdo->prepare("SELECT p.id, p.created_at, p.title, COUNT(r.id) AS res_count, u.name 
                           FROM Posts p
                           LEFT JOIN Posts r ON r.parent_id = p.id
                           LEFT JOIN Users u ON p.user_id = u.id
                           WHERE p.parent_id IS NULL AND p.is_deleted = 0
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

    // 投稿数を取得(全投稿)
    // $stmt = $pdo->query("SELECT COUNT(*) AS total FROM Posts WHERE parent_id IS NULL")$total_posts = $stmt->fetch()['total'];

    // 投稿数を取得（削除された投稿は除外）
    $stmt = $pdo->query("SELECT COUNT(*) AS total FROM Posts WHERE parent_id IS NULL AND is_deleted = 0");
    $total_posts = $stmt->fetch()['total'];


    // 総ページ数を計算
    return ceil($total_posts / $per_page);
}

// IDに基づいて特定の投稿を取得する関数
function getPostById($id)
{
    global $pdo;
    $stmt = $pdo->prepare("SELECT Posts.id, Posts.title, Posts.content, Users.name, Users.id AS user_id, Posts.created_at 
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
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'update') {
    updatePost($_POST['id'], $_POST['title'], $_POST['content']);
    header('Location: ../Views/post_list.php');
    exit;
}



// 投稿を削除する関数 (論理削除)

// 投稿を削除する関数 (投稿者または管理者用)
function deletePost($post_id, $user_id, $is_admin)
{
    global $pdo;

    if ($is_admin) {
        // 管理者はどの投稿でも削除可能
        $stmt = $pdo->prepare("UPDATE Posts SET is_deleted = 1 WHERE id = ?");
        $stmt->execute([$post_id]);
    } else {
        // 投稿者本人のみ削除可能
        $stmt = $pdo->prepare("UPDATE Posts SET is_deleted = 1 WHERE id = ? AND user_id = ?");
        $stmt->execute([$post_id, $user_id]);

        if ($stmt->rowCount() === 0) { // 削除された行がない場合
            // 削除権限がない場合のエラーメッセージ
            echo "削除できる権限がありません。";
            exit;
        }
    }
}




// レスポンスを取得する関数(親IDに基づいて)
function getResponses($parent_id)
{
    global $pdo;

    // レスポンスを取得（名前情報を結合.この処理をしないと名前が取得できない）
    $stmt = $pdo->prepare("SELECT Posts.id, Posts.content, Posts.created_at, Users.name FROM Posts 
        JOIN Users ON Posts.user_id = Users.id
        WHERE Posts.parent_id = ? AND Posts.is_deleted = 0 
        ORDER BY Posts.created_at ASC
    ");
    $stmt->execute([$parent_id]); // 実行
    return $stmt->fetchAll(); // 結果を返す
}


function createPost($user_id, $title, $content)
{
    global $pdo;

    $stmt = $pdo->prepare("INSERT INTO Posts (user_id, title, content, created_at, updated_at) VALUES (?, ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)");
    $stmt->execute([$user_id, $title, $content]);
}



// レスポンス投稿処理
function createResponse($user_id, $parent_id, $content)
{
    global $pdo;

    // セッション開始（セッションが既に開始されている場合は何もしない）
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // 親投稿に既にレスポンスがあるか確認
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM Posts WHERE parent_id = ? AND is_deleted = 0");
    $stmt->execute([$parent_id]);
    $response_count = $stmt->fetchColumn();

    // レスポンスが既に2件ある場合、同じページでレスポンスは1件までとする。javascriptで制御
    if ($response_count >= 2) {
        // セッションにエラーメッセージを保存
        $_SESSION['flash_message'] = "レスポンスは2件までです。";

        // 元の投稿ページへリダイレクト
        header('Location: ../Views/post_detail.php?id=' . $parent_id);
        exit;
    }

    // レスポンスを新規作成
    $stmt = $pdo->prepare("INSERT INTO Posts (user_id, parent_id, content, created_at, updated_at) VALUES (?, ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)");
    $stmt->execute([$user_id, $parent_id, $content,]);
}

// レスの更新
function updateResponse($id, $content)
{
    global $pdo;
    $stmt = $pdo->prepare("UPDATE Posts SET content = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ? AND is_deleted = 0");
    $stmt->execute([$content, $id]);
    echo "レスが更新されました！";
}
