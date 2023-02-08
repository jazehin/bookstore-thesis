<?php
include("includes/header.php");
?>

<div class="d-flex flex-row flex-nowrap overflow-scroll">
    <?php for ($i = 0; $i < 5; $i++) { ?>
        <div class="bookcard flex-column m-2 ms-0">
            <img class="bookcover mx-auto mt-3" src="https://s06.static.libri.hu/cover/55/1/9517963_5.jpg" alt="borító"
                title="borító">

            <div class="bookinfo">
                <span class="fw-bold fs-5 lh-1">Mindenki titkol valamit</span><br>
                <span class="fst-italic">Jane Corry</span>
                <!--
                    <span class="bookdesc-text">
                        Két nő a Willowmead House-ban.<br>
                        Az egyik menekül.<br>
                        A másik bujkál.<br>
                        Mindkettő hazudik.
                    </span>
                    -->
                <div class="flex-row pt-2">
                    <span>2499 Ft</span>
                    <a href="" class="btn btn-primary">Megnézem</a>
                </div>
            </div>
        </div>
    <?php } ?>
</div>

<?php
include("includes/footer.php");

?>