<?php
$bookdata = GetBookByISBN($_GET["isbn"]);
?>

<div class="card my-3 p-3">
    <div class="row">
        <span class="fw-bold fs-5 lh-1 mb-0">
            <?php echo $bookdata["konyvcim"]; ?>
        </span><br>
        <span class="fst-italic pb-2">Adam Silvera</span>
        <hr class="">
    </div>
    <div class="row">
        <div class="col-md-4 col-lg-3">
            <img class="img-fluid" src="/covers/<?php echo $bookdata["isbn"] ?>.jpg" alt="">
            <div class="book-info">
                <div class="row pt-1">
                    <span class="col-5 my-auto fw-bold">Kiadó:</span>
                    <span class="col-7 my-auto">
                        <?php echo $bookdata["kiado"]; ?>
                    </span>
                </div>

                <div class="row pt-1">
                    <span class="col-5 my-auto fw-bold">ISBN:</span>
                    <span class="col-7 my-auto">
                        <?php echo $bookdata["isbn"]; ?>
                    </span>
                </div>

                <?php if (!empty($bookdata["suly"])) { ?>
                    <div class="row pt-1">
                        <span class="col-5 my-auto fw-bold">Súly:</span>
                        <span class="col-7 my-auto">
                            <?php echo $bookdata["suly"]; ?> g
                        </span>
                    </div>
                <?php } ?>

                <div class="row pt-1">
                    <span class="col-5 my-auto fw-bold">Kötéstípus:</span>
                    <span class="col-7 my-auto">
                        <?php echo $bookdata["kotestipus"]; ?>
                    </span>
                </div>

                <div class="row pt-1">
                    <span class="col-5 my-auto fw-bold">Kiadás dátuma:</span>
                    <span class="col-7 my-auto">
                        <?php
                        $date = date_create($bookdata["kiadasdatuma"]);
                        echo date_format($date, "Y. m. d.");
                        ?>
                    </span>
                </div>

                <div class="row pt-1">
                    <span class="col-5 my-auto fw-bold">Nyelv:</span>
                    <span class="col-7 my-auto">
                        <?php echo $bookdata["nyelv"]; ?>
                    </span>
                </div>

                <div class="row py-1">
                    <span class="col-5 my-auto fw-bold">Oldalak száma:</span>
                    <span class="col-7 my-auto">
                        <?php echo $bookdata["oldalszam"]; ?>
                    </span>
                </div>

                

            </div>
        </div>
        <div class="col-md-8 col-lg-9">
            <div class="book-content">
                <span class="fw-bold">Leírás:</span><br>
                <span>
                    <?php echo str_replace("\n", "<br>", $bookdata["leiras"]); ?>
                </span>
            </div>
        </div>
    </div>


    <!--<i class="fa-solid fa-basket-shopping fa-xl"></i>-->
</div>