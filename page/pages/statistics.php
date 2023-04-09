<?php if ($_SESSION["logged_in"] && $_SESSION["user"]["type"] === "administrator") { ?>

    <h2 class="fs-3">Statisztikák</h2>
    <div class="row">
        <div class="col-lg-6">
            <h3 class="fs-5">A legtöbb bevételt hozó írók</h3>
            <table class="table mb-3">
                <thead class="table-head-brown">
                    <tr>
                        <th>Író neve</th>
                        <th>Eladott könyvek száma</th>
                        <th>Befolyt bevétel</th>
                    </tr>
                </thead>
                <tbody class="text-center-phones align-middle">
                    <?php $rs = GetStatistics("sales_by_writers"); ?>
                    <?php while ($row = mysqli_fetch_row($rs)) { ?>
                    <tr>
                        <td>
                            <a href="/authors/<?php echo str_replace(" ", "+", $row[0]) ?>"><?php echo $row[0]; ?></a>
                        </td>
                        <td>
                            <?php echo "$row[1] db"; ?>
                        </td>
                        <td>
                            <?php echo "$row[2] Ft" ?>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>


            <h3 class="fs-5">A legtöbb bevételt hozó műfajok</h3>
            <table class="table mb-3">
                <thead class="table-head-brown">
                    <tr>
                        <th>Műfaj neve</th>
                        <th>Eladott könyvek száma</th>
                        <th>Befolyt bevétel</th>
                    </tr>
                </thead>
                <tbody class="text-center-phones align-middle">
                    <?php $rs = GetStatistics("sales_by_genres"); ?>
                    <?php while ($row = mysqli_fetch_row($rs)) { ?>
                    <tr>
                        <td>
                            <?php echo $row[0]; ?>
                        </td>
                        <td>
                            <?php echo "$row[1] db"; ?>
                        </td>
                        <td>
                            <?php echo "$row[2] Ft" ?>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>

            <h3 class="fs-5">A legtöbb bevételt hozó kiadók</h3>
            <table class="table mb-3">
                <thead class="table-head-brown">
                    <tr>
                        <th>Kiadó neve</th>
                        <th>Eladott könyvek száma</th>
                        <th>Befolyt bevétel</th>
                    </tr>
                </thead>
                <tbody class="text-center-phones align-middle">
                    <?php $rs = GetStatistics("sales_by_publishers"); ?>
                    <?php while ($row = mysqli_fetch_row($rs)) { ?>
                    <tr>
                        <td>
                        <a href="/publishers/<?php echo str_replace(" ", "+", $row[0]) ?>"><?php echo $row[0]; ?></a>
                        </td>
                        <td>
                            <?php echo "$row[1] db"; ?>
                        </td>
                        <td>
                            <?php echo "$row[2] Ft" ?>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <div class="col-lg-6">
            <h3 class="fs-5">Az elmúlt hét legtöbbet eladott könyvei</h3>
            <table class="table mb-3">
                <thead class="table-head-brown">
                    <tr>
                        <th>Könyv neve</th>
                        <th>Eladott példányszám</th>
                        <th>Befolyt bevétel</th>
                    </tr>
                </thead>
                <tbody class="text-center-phones align-middle">
                    <?php $rs = GetStatistics("sales_of_last_week"); ?>
                    <?php while ($row = mysqli_fetch_row($rs)) { ?>
                    <tr>
                        <td>
                        <a href="/books/<?php echo $row[0]; ?>"><?php echo $row[1]; ?></a>
                        </td>
                        <td>
                            <?php echo "$row[2] db"; ?>
                        </td>
                        <td>
                            <?php echo "$row[3] Ft" ?>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
            <h3 class="fs-5">Az elmúlt hónap legtöbbet eladott könyvei</h3>
            <table class="table mb-3">
                <thead class="table-head-brown">
                    <tr>
                        <th>Könyv neve</th>
                        <th>Eladott példányszám</th>
                        <th>Befolyt bevétel</th>
                    </tr>
                </thead>
                <tbody class="text-center-phones align-middle">
                    <?php $rs = GetStatistics("sales_of_last_month"); ?>
                    <?php while ($row = mysqli_fetch_row($rs)) { ?>
                    <tr>
                        <td>
                        <a href="/books/<?php echo $row[0]; ?>"><?php echo $row[1]; ?></a>
                        </td>
                        <td>
                            <?php echo "$row[2] db"; ?>
                        </td>
                        <td>
                            <?php echo "$row[3] Ft" ?>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
            <h3 class="fs-5">Az elmúlt negyedév legtöbbet eladott könyvei</h3>
            <table class="table mb-3">
                <thead class="table-head-brown">
                    <tr>
                        <th>Könyv neve</th>
                        <th>Eladott példányszám</th>
                        <th>Befolyt bevétel</th>
                    </tr>
                </thead>
                <tbody class="text-center-phones align-middle">
                    <?php $rs = GetStatistics("sales_of_last_quarter"); ?>
                    <?php while ($row = mysqli_fetch_row($rs)) { ?>
                    <tr>
                        <td>
                        <a href="/books/<?php echo $row[0]; ?>"><?php echo $row[1]; ?></a>
                        </td>
                        <td>
                            <?php echo "$row[2] db"; ?>
                        </td>
                        <td>
                            <?php echo "$row[3] Ft" ?>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
            <h3 class="fs-5">Az elmúlt év legtöbbet eladott könyvei</h3>
            <table class="table mb-3">
                <thead class="table-head-brown">
                    <tr>
                        <th>Könyv neve</th>
                        <th>Eladott példányszám</th>
                        <th>Befolyt bevétel</th>
                    </tr>
                </thead>
                <tbody class="text-center-phones align-middle">
                    <?php $rs = GetStatistics("sales_of_last_year"); ?>
                    <?php while ($row = mysqli_fetch_row($rs)) { ?>
                    <tr>
                        <td>
                        <a href="/books/<?php echo $row[0]; ?>"><?php echo $row[1]; ?></a>
                        </td>
                        <td>
                            <?php echo "$row[2] db"; ?>
                        </td>
                        <td>
                            <?php echo "$row[3] Ft" ?>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

            

<?php } else { ?>
    <p class="text-danger">Nincs jogosultsága az oldal megtekintéséhez!</p>
<?php } ?>