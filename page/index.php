<?php
include("includes/header.php");
?>



<div class="bookcard-container d-flex flex-lg-wrap overflow-scroll mt-4">
    <?php for ($i = 0; $i < 10; $i++) { ?>
        <div class="bookcard flex-column position-relative m-2 p-2 ms-0 shadow-sm">
            <img class="bookcover" src="https://s04.static.libri.hu/cover/b3/5/4468082_5.jpg" alt="borító"
                title="borító">

            <div class="bookinfo m-2">
                <span class="booktitle fw-bold fs-5 lh-1 mb-0">The subtle art of not giving a fuckity fuck</span><br>
                <span class="fst-italic">Kathleen Glasgow</span>
                <div class="flex-row pt-2">
                    <span class="text-decoration-line-through">2499 Ft</span>
                    <span class="text-danger fw-bold">1699 Ft</span>
                </div>
                <a href="" class="btn w-100 stretched-link" style="background-color: #8B5E3C; color: white;">Megnézem</a>
            </div>
        </div>
    <?php } ?>
</div>

<?php
include("includes/footer.php");

?>