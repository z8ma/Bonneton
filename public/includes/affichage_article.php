<?php
echo "<a href='page_details_article.php?id=" . $row["id"] . "'><div class='article'>";
echo "<div class='left-section'>";
echo "<br><br><h2>" . $row["article_name"] . "</h2><br>";
echo "<p>Prix : " . $row["prix"] . " €</p>";
echo "</div>";
echo "<div class='right-section'>";
echo "<img src='" . $row["img"] . "' alt='" . $row["article_name"] . "'>";
echo "</div>";
echo "</div></a>";
