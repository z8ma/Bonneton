<?php
echo "<a href='page_details_article.php?id=" . e($row["id"]) . "'><div class='article'>";
echo "<div class='left-section'>";
echo "<br><br><h2>" . e($row["article_name"]) . "</h2><br>";
echo "<p>Prix : " . e($row["prix"]) . " €</p>";
echo "</div>";
echo "<div class='right-section'>";
echo "<img src='" . e($row["img"]) . "' alt='" . e($row["article_name"]) . "'>";
echo "</div>";
echo "</div></a>";
