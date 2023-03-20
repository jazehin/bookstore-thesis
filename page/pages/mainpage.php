<?php
$books = GetISBNs();
?>

<div class="bookcard-container d-flex flex-lg-wrap overflow-scroll mt-4">
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
        ?>
        <div class="bookcard flex-column position-relative m-2 p-2 ms-0 shadow-sm">
            <img class="bookcard-cover" src="<?php echo $folder . $img ?>" alt="<?php echo $folder . $img ?>"
                title="<?php echo $bookdata["title"]; ?> borító">

            <div class="bookcard-info m-2">
                <span class="bookcard-title fw-bold fs-5 lh-1 mb-0">
                    <?php echo $bookdata["title"]; ?>
                </span><br>
                <span class="fst-italic"><?php echo $writers ?></span>
                <div class="flex-row pt-2">
                    <span class="text-decoration-line-through">
                        <?php echo $bookdata["price"] ?> Ft
                    </span>

                    <span class="text-danger fw-bold">
                        <?php echo $bookdata["discounted_price"] ?> Ft
                    </span>
                </div>
                <a href="/books/<?php echo $bookdata["isbn"]; ?>" class="btn w-100 stretched-link" style="background-color: #8B5E3C; color: white;">Megnézem</a>
            </div>
        </div>
    <?php } ?>
</div>