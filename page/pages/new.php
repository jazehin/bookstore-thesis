<?php
$page = $_GET["page"];

$books_per_page = 10;
$types = "";
$vars = [];

$sqlnum =  "SELECT isbn FROM new_books";
$number_of_results = GetNumberBooksForPage($sqlnum, $types, $vars);
$number_of_pages = ceil($number_of_results / $books_per_page);

$offset = ($page - 1) * $books_per_page;
$sqlbooks = $sqlnum . " LIMIT $offset, $books_per_page";
$books = GetBooksForPage($page, $books_per_page, $sqlbooks, $types, $vars);
?>

<h2 class="fs-3">Újdonságok az elmúlt 1 hónapból</h2>

<div class="bookcard-container d-flex flex-wrap overflow-scroll mt-4">
    <?php for ($i = 0; $i < count($books); $i++) {
        $bookdata = GetBookByISBN($books[$i]);

        $folder = "covers/";
        $img = "no_cover.jpg";
        if (file_exists($folder . $bookdata["isbn"] . ".jpg")) {
            $img = $bookdata["isbn"] . ".jpg";
        } else if (file_exists($folder . $bookdata["isbn"] . ".jpeg")) {
            $img = $bookdata["isbn"] . ".jpeg";
        } else if (file_exists($folder . $bookdata["isbn"] . ".png")) {
            $img = $bookdata["isbn"] . ".png";
        }



        $writers = "";
        for ($j = 0; $j < count($bookdata["writers"]); $j++) {
            $writers = $writers . $bookdata["writers"][$j][1];
            if ($j < count($bookdata["writers"]) - 1)
                $writers = $writers . ', ';
        }

        $bookdata["rating"] = GetAvgRatingByISBN($bookdata["isbn"]);
        ?>

        <div class="bookcard m-2 p-2">
            <div class="row position-relative">
                <div class="col-lg-auto col-12">
                    <div class="bookcard-cover m-3">
                        <img class="img-fluid" src="/<?php echo $folder . $img ?>" alt="<?php echo $folder . $img ?>">
                    </div>
                </div>
                <div class="col-lg col-12">
                    <div class="bookcard-content">
                        <span class="bookcard-title fw-bold">
                            <?php echo $bookdata["title"]; ?>
                        </span>
                        <span class="bookcard-writer fst-italic">
                            <?php echo $writers ?>
                        </span>
                        <div class="my-2">
                            <span class="bookcard-rating">
                                <?php for ($k = 1; $k < 6; $k++) { ?>
                                    <?php if ($k <= $bookdata["rating"][0]) { ?>
                                        <i class="fa-solid fa-star"></i>
                                    <?php } elseif (0.5 == $k - $bookdata["rating"][0]) { ?>
                                        <i class="fa-solid fa-star-half-stroke"></i>
                                    <?php } else { ?>
                                        <i class="fa-regular fa-star"></i>
                                    <?php } ?>
                                <?php } ?>
                            </span>
                            <span class="rating-count small text-secondary fst-italic ms-2">
                                <?php echo $bookdata["rating"][1] ?> értékelés
                            </span>
                        </div>
                        <span class="bookcard-description mb-2">
                            <?php echo stripslashes($bookdata["description"]); ?>
                        </span>
                        <span class="bookcard-price <?php if (!is_null($bookdata["discounted_price"]))
                            echo "text-decoration-line-through" ?>"><?php echo $bookdata["price"]; ?> Ft</span>
                        <?php if (!is_null($bookdata["discounted_price"])) { ?>
                            helyett <span class="bookcard-discounted-price text-danger fw-bold">
                                <?php echo $bookdata["discounted_price"]; ?> Ft
                            </span>
                            <span class="discount-percent">
                                (-
                                <?php echo round(((1 - ($bookdata["discounted_price"] / $bookdata["price"])) * 100), 0); ?>%)
                            </span>
                        <?php } ?>
                    </div>
                </div>
                <a href="/books/<?php echo $bookdata["isbn"]; ?>" class="stretched-link"></a>
            </div>
        </div>
    <?php } ?>
</div>

<?php if ($number_of_pages > 1) { ?>
    <ul class="pagination">
        <li class="page-item">
            <a class="page-link <?php if ($page == 1)
                echo 'disabled'; ?>" <?php if ($page == 1)
                      echo 'tabindex="-1"'; ?>
                href="/new/<?php echo $page - 1; ?>" aria-label="Previous">
                <span aria-hidden="true">&laquo;</span>
            </a>
        </li>
        <?php if ($page - 2 > 0) { ?>
            <li class="page-item"><a class="page-link"
                    href="/new/<?php echo $page - 2; ?>"><?php echo $page - 2; ?></a></li>
        <?php } ?>
        <?php if ($page - 1 > 0) { ?>
            <li class="page-item"><a class="page-link"
                    href="/new/<?php echo $page - 1; ?>"><?php echo $page - 1; ?></a></li>
        <?php } ?>
        <li class="page-item active" aria-current="page"><a class="page-link" href="">
                <?php echo $page; ?>
            </a></li>
        <?php if ($page < $number_of_pages) { ?>
            <li class="page-item"><a class="page-link"
                    href="/new/<?php echo $page + 1; ?>"><?php echo $page + 1; ?></a></li>
        <?php } ?>
        <?php if ($page + 1 < $number_of_pages) { ?>
            <li class="page-item"><a class="page-link"
                    href="/new/<?php echo $page + 2; ?>"><?php echo $page + 2; ?></a></li>
        <?php } ?>
        <li class="page-item">
            <a class="page-link <?php if ($page == $number_of_pages)
                echo 'disabled'; ?>" <?php if ($page == $number_of_pages)
                      echo 'tabindex="-1"'; ?>
                href="/new/<?php echo $page + 1; ?>" aria-label="Next">
                <span aria-hidden="true">&raquo;</span>
            </a>
        </li>
    </ul>
<?php } ?>