<div class="d-flex flex-row overflow-scroll">
    <?php for ($i = 0; $i < 10; $i++) { ?>
        <div class="bookcard flex-column m-2 p-2 ms-0 shadow-sm">
            <img class="bookcover mx-auto" src="https://s04.static.libri.hu/cover/b3/5/4468082_5.jpg" alt="borító"
                title="borító">

            <div class="bookinfo m-2">
                <span class="fw-bold fs-5 lh-1">The subtle art of not giving a fuck</span><br>
                <span class="fst-italic">Kathleen Glasgow</span>
                <!--
                    <span class="bookdesc-text">
                        Két nő a Willowmead House-ban.<br>
                        Az egyik menekül.<br>
                        A másik bujkál.<br>
                        Mindkettő hazudik.
                    </span>
                    -->
                <div class="flex-row pt-2">
                    <span class="text-decoration-line-through">2499 Ft</span>
                    <span class="text-danger fw-bold">1699 Ft</span>
                    <a href="" class="btn btn-light w-100" style="background-color: #8B5E3C; color: white;">Megnézem</a>
                </div>
            </div>
        </div>
    <?php } ?>
</div>