<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BLOG WEBSITE</title>
    <style>
        body {
            box-sizing: border-box;
            font-family: Arial, sans-serif;
            background-image:url(background.jpg);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f4f4f4;
        }

        .container {
            width: 60%;
            margin: 0 auto;
        }

        button {
            margin: 20px 0;
            padding: 10px 20px;
            font-size: 16px;
        }

        #posts {
            margin-top: 20px;
        }

        .post {
            background-color: black;
            padding: 20px;
            margin: 10px 0;
            color: #f4f4f4;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: darkgrey;
        }

        .modal-content {
            background-color:Black;
            margin: 15% auto;
            padding: 30px;
            font-family: 'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif;
            color:burlywood;
            border-radius: 16px;
            width: 80%;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        .post button {
            margin: 5px;
            padding: 10px 10px;
            color: aqua;
            background-color:grey;
        }

        .cl0 {
            background-color: yellow;
            color: black;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }

        .cl1 {
            color: #fff;
        }
    </style>
</head>
<body>

<div class="container">
    <h1 class="cl0">BLOG WEBSITE</h1>
    <h1 class="cl1" style="font-family: Georgia, 'Times New Roman', Times, serif;">Make a Blog Post</h1>
    <button onclick="showCreatePostForm()">Create New Post</button>
    <div id="posts">
    <?php
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "DEPTASK2";

        // Create connection
        $conn = new mysqli($servername, $username, $password);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Create database if not exists
        $sql = "CREATE DATABASE IF NOT EXISTS $dbname";
        if ($conn->query($sql) === FALSE) {
            die("Error creating database: " . $conn->error);
        }

        // Select the database
        $conn->select_db($dbname);

        // Create table if not exists
        $sql = "CREATE TABLE IF NOT EXISTS posts (
            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            content TEXT NOT NULL,
            reg_date TIMESTAMP
        )";
        if ($conn->query($sql) === FALSE) {
            die("Error creating table: " . $conn->error);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['create'])) {
                $title = $conn->real_escape_string($_POST['title']);
                $content = $conn->real_escape_string($_POST['content']);
                $sql = "INSERT INTO posts (title, content) VALUES ('$title', '$content')";
                if ($conn->query($sql) === FALSE) {
                    die("Error: " . $sql . "<br>" . $conn->error);
                }
            } elseif (isset($_POST['update'])) {
                $id = $conn->real_escape_string($_POST['id']);
                $title = $conn->real_escape_string($_POST['title']);
                $content = $conn->real_escape_string($_POST['content']);
                $sql = "UPDATE posts SET title='$title', content='$content' WHERE id=$id";
                if ($conn->query($sql) === FALSE) {
                    die("Error: " . $sql . "<br>" . $conn->error);
                }
            } elseif (isset($_POST['delete'])) {
                $id = $conn->real_escape_string($_POST['id']);
                $sql = "DELETE FROM posts WHERE id=$id";
                if ($conn->query($sql) === FALSE) {
                    die("Error: " . $sql . "<br>" . $conn->error);
                }
            }
        }

        $sql = "SELECT * FROM posts";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<div class='post'>";
                echo "<h2>" . htmlspecialchars($row['title']) . "</h2>";
                echo "<p>" . htmlspecialchars($row['content']) . "</p>";
                echo "<form method='POST' style='display:inline;'>
                        <input type='hidden' name='id' value='" . $row['id'] . "'>
                        <button type='button' onclick='showEditPostForm(" . $row['id'] . ", \"" . htmlspecialchars($row['title']) . "\", \"" . htmlspecialchars($row['content']) . "\")'>Edit</button>
                        <button type='submit' name='delete'>Delete</button>
                      </form>";
                echo "</div>";
            }
        } else {
            echo "0 results";
        }

        $conn->close();
        ?>
    </div>
</div>

<div id="create-post-form" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeCreatePostForm()">&times;</span>
        <h2>Create New Post</h2>
        <form method="POST">
            <input type="text" name="title" placeholder="Title" required><br><br>
            <textarea name="content" placeholder="Content" required></textarea><br>
            <button type="submit" name="create">Create</button>
        </form>
    </div>
</div>

<div id="edit-post-form" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeEditPostForm()">&times;</span>
        <h2>Edit Post</h2>
        <form method="POST">
            <input type="hidden" id="edit-post-id" name="id">
            <input type="text" id="edit-post-title" name="title" placeholder="Title" required>
            <textarea id="edit-post-content" name="content" placeholder="Content" required></textarea>
            <button type="submit" name="update">Update</button>
        </form>
    </div>
</div>

<script>
    function showCreatePostForm() {
        document.getElementById("create-post-form").style.display = "block";
    }

    function closeCreatePostForm() {
        document.getElementById("create-post-form").style.display = "none";
    }

    function showEditPostForm(id, title, content) {
        document.getElementById("edit-post-id").value = id;
        document.getElementById("edit-post-title").value = title;
        document.getElementById("edit-post-content").value = content;
        document.getElementById("edit-post-form").style.display = "block";
    }

    function closeEditPostForm() {
        document.getElementById("edit-post-form").style.display = "none";
    }
</script>

</body>
</html>
