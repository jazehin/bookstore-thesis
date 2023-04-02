<?php
include("./../../includes/db_con.php");
include("./../../includes/sql.php");
session_start();

$isbn = $_GET["isbn"];
$page = intval($_GET["page"]);

$comments_per_page = 10;
$number_of_comments = GetNumberOfComments($isbn);
$number_of_pages = ceil($number_of_comments / $comments_per_page);
$comments = GetComments($isbn, $comments_per_page, $page);

?>

<?php while ($row = mysqli_fetch_row($comments)) { ?>
    <div class="row comment mb-3">
        <div class="comment-info w-100">
            <span class="comment-id text-secondary small">
                #<?php echo $row[0]; ?>
            </span>
            <span class="fw-bold">
                <?php echo $row[1]; ?>
            </span> <span class="small">
                <?php echo $row[3]; ?>
            </span>
            <?php if ($_SESSION["logged_in"] && ($_SESSION["user"]["username"] == $row[1] || $_SESSION["user"]["type"] == "administrator" || $_SESSION["user"]["type"] == "moderator")) { ?>
                <span class="float-end text-end">
                    <input type="button" onclick="setDeleteCommentModal(<?php echo $row[0]; ?>);"
                        class="btn btn-link link-danger fw-normal p-0" value="Törlés" data-bs-toggle="modal"
                        data-bs-target="#deleteCommentModal">
                </span>
            <?php } ?>
        </div>
        <span class="comment-text">
            <?php echo $row[2]; ?>
        </span>
    </div>
<?php } ?>

<?php if ($number_of_pages > 1) { ?>
        <ul class="pagination">
            <li class="page-item">
                <input type="button" class="page-link <?php if ($page == 1)
                    echo 'disabled'; ?>" <?php if ($page == 1)
                          echo 'tabindex="-1"'; ?> value="&laquo;" aria-label="Previous"
                          onclick="loadComments('<?php echo $isbn ?>', <?php echo $page - 1; ?>)">
            </li>
            <?php if ($page - 2 > 0) { ?>
                <li class="page-item">
                <input type="button" class="page-link" value="<?php echo $page - 2; ?>"
                          onclick="loadComments('<?php echo $isbn; ?>', <?php echo $page - 2; ?>)">    
                </li>
            <?php } ?>
            <?php if ($page - 1 > 0) { ?>
                <li class="page-item">
                <input type="button" class="page-link" value="<?php echo $page - 1; ?>"
                          onclick="loadComments('<?php echo $isbn; ?>', <?php echo $page - 1; ?>)">    
                </li>
            <?php } ?>
                <li class="page-item">
                <input type="button" class="page-link active" value="<?php echo $page; ?>">    
                </li>
            <?php if ($page < $number_of_pages) { ?>
                <li class="page-item">
                <input type="button" class="page-link" value="<?php echo $page + 1; ?>"
                          onclick="loadComments('<?php echo $isbn; ?>', <?php echo $page + 1; ?>)">    
                </li>
            <?php } ?>
            <?php if ($page + 1 < $number_of_pages) { ?>
                <li class="page-item">
                <input type="button" class="page-link" value="<?php echo $page + 2; ?>"
                          onclick="loadComments('<?php echo $isbn; ?>', <?php echo $page + 2; ?>)">    
                </li>
            <?php } ?>
            <li class="page-item">
                <input type="button" class="page-link <?php if ($page == $number_of_pages)
                    echo 'disabled'; ?>" <?php if ($page == $number_of_pages)
                          echo 'tabindex="-1"'; ?> value="&raquo;" aria-label="Next"
                          onclick="loadComments('<?php echo $isbn ?>', <?php echo $page + 1; ?>)">
            </li>
            <!--
            <li class="page-item">
                <a class="page-link <?php if ($page == $number_of_pages)
                    echo 'disabled'; ?>" <?php if ($page == $number_of_pages)
                          echo 'tabindex="-1"'; ?>
                    href="/search/<?php echo urlencode($query); ?>/<?php echo $page + 1; ?>" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
            -->
        </ul>
    <?php } ?>