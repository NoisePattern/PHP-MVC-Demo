<?php

echo '<h1>' . $article['caption'] . '</h1>';
echo '<p>Posted by ' . $article['author'] . ' on ' . date('j.n.Y H:i', strtotime($article['created']));
if(!is_null($article['updated'])){
	echo ', edited on ' . date('j.n.Y H:i', strtotime($article['updated']));
}
echo '</p>';
echo html_entity_decode($article['content']);

?>