<?php
session_start();
require_once("SQLconnection.php");

if (isset($_POST['login'])) {
    $_SESSION['activeUser'] = $_POST['username'];
}

/*
 * If like or dislike button is pressed.
 */
if (isset($_POST['like']) or (isset($_POST['dislike']))) {

    $user = $_POST['username'];
    echo("<script>console.log('" . $user . "');</script>");
    $news = $_POST['newsId'];
    echo("<script>console.log('" . $news . "');</script>");
    $likes = isset($_POST['like']) ? $_POST['like'] : $_POST['dislike'];
    echo("<script>console.log('" . $likes . "');</script>");


    $SQLconnectionScore = new SQLconnection();
    $dataBaseConnectionScore = $SQLconnectionScore->ConnectSQL();
    $statementScore = $dataBaseConnectionScore->prepare("
               INSERT INTO 0_sk_score(username, newsid, likes)
               VALUES(:Username, :News, :SuperScore)
             ");
    $statementScore->bindParam(':Username', $user, PDO::PARAM_INT);
    $statementScore->bindParam(':News', $news, PDO::PARAM_INT);
    $statementScore->bindParam(':SuperScore', $likes, PDO::PARAM_INT);
    $statementScore->execute();

    $result = $statementScore->fetchAll(PDO::FETCH_ASSOC);
    $dataBaseConnectionScore = null;
}

/*
 * If post button is pressed.
 */
if (isset($_POST['username']) && isset($_POST['news'])) {
    $user = $_POST['username'];
    $news = $_POST['news'] == "" ? "I have nothing to say" : $_POST['news'];

    $SQLconnectionPosts = new SQLconnection();
    $dataBaseConnectionPosts = $SQLconnectionPosts->ConnectSQL();
    $statementPosts = $dataBaseConnectionPosts->prepare("
               INSERT INTO 0_sk_posts(username, news)
               VALUES(:Username, :News)
             ");
    $statementPosts->bindParam(':Username', $user, PDO::PARAM_INT);
    $statementPosts->bindParam(':News', $news, PDO::PARAM_INT);
    $statementPosts->execute();
    $result = $statementPosts->fetchAll(PDO::FETCH_ASSOC);
    $dataBaseConnectionPosts = null;
}

/*
 * If logout button is pressed.
 */
if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Mini reddit</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div>
    <div class="loginHeader">
        <form class="loginHeaderForm" method="post">

            <?php if (!isset($_SESSION['activeUser'])) { ?>
                <input type="text" name="username" placeholder="username" required autofocus>
                <button type="submit" name="login">log in</button>
            <?php } else { ?>
                <button type="submit" name="logout">log out <?php echo $_SESSION['activeUser'] ?></button>
            <?php } ?>
        </form>
    </div>

    <div class="container">
        <?php if (isset($_SESSION['activeUser'])) { ?>
            <div class="poster">
                <form method="post" id="newsPostingForm">
                    <input type="text" name="news" id="newsFormText">
                    <input type="hidden" name="username" value="<?php echo $_SESSION['activeUser'] ?>">
                    <button type="submit" class="btn" name="postNew">Post</button>
                </form>
            </div>

        <?php }

        /*
         * Get request, prints the page.
         */

        $SQLconnectionGetScore = new SQLconnection();
        $dataBaseConnectionGetScore = $SQLconnectionGetScore->ConnectSQL();
        $statementGetScore = $dataBaseConnectionGetScore->prepare("
               SELECT 0_sk_posts.id, 0_sk_posts.username, 0_sk_posts.news, timeOfPost
               FROM 0_sk_posts;
             ");
        $statementGetScore->bindParam(':Username', $user, PDO::PARAM_INT);
        $statementGetScore->bindParam(':News', $news, PDO::PARAM_INT);
        $statementGetScore->bindParam(':Likes', $likes, PDO::PARAM_INT);
        $statementGetScore->execute();
        $result = $statementGetScore->fetchAll(PDO::FETCH_ASSOC);
        $dataBaseConnectionGetScore = null;

        foreach ($result as $value) {

            $SQLconnectionGetScoreLikes = new SQLconnection();
            $dataBaseConnectionGetScoreLikes = $SQLconnectionGetScore->ConnectSQL();
            $statementGetScoreLikes = $dataBaseConnectionGetScoreLikes->prepare("
            SELECT SUM(likes) AS likes
            FROM 0_sk_score
            WHERE newsid = :News
            ");
            $statementGetScoreLikes->bindParam(':News', $value["id"], PDO::PARAM_INT);
            $statementGetScoreLikes->execute();
            $likes = $statementGetScoreLikes->fetchAll(PDO::FETCH_ASSOC);
            $dataBaseConnectionGetScoreLikes = null;
            ?>
        <div class="news">
            <p class="newsText"><?php echo htmlentities($value["news"]) ?></p>
            <p class="newsFooter"><?php echo $value["username"] ?> posted on <?php echo $value["timeOfPost"] ?>
                Likes: <?php echo (gettype($likes[0]['likes']) == 'string' ? $likes[0]['likes'] : 0); ?></p>
            <form method="post" id="likeButton">
                <input type="hidden" name="newsId" value="<?php echo $value["id"] ?>">
                <input type="hidden" name="username" value="<?php echo $value["username"] ?>">
                <?php if (isset($_SESSION['activeUser'])) { ?>
                    <button type="submit" class="btn" name="like" value="1">like</button>
                    <button type="submit" class="btn" name="dislike" value="-1">dislike</button>
                <?php } ?>
            </form>
        </div>
    <?php } ?>


    </div>
</body>
</html>