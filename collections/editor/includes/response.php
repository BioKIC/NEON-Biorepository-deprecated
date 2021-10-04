<?php
include_once('../../../config/symbini.php');
include_once('fp/FPNetworkFactory.php');
include_once('fp/common/AnnotationGenerator.php');
if($LANG_TAG != 'en' && file_exists($SERVER_ROOT.'/content/lang/collections/editor/includes/response.'.$LANG_TAG.'.php')) include_once($SERVER_ROOT.'/content/lang/collections/editor/includes/response.'.$LANG_TAG.'.php');
else include_once($SERVER_ROOT.'/content/lang/collections/editor/includes/response.en.php');

// check that the client helper has been installed
$file = 'fp/FPNetworkFactory.php';
$includePaths = explode(PATH_SEPARATOR, get_include_path());
$fileExists = false;

foreach ($includePaths as $p) {
    $fullname = $p . DIRECTORY_SEPARATOR . $file;
    if (is_file($fullname)) {
        $fileExists = true;
        break;
    }
}

if (!$fileExists) {
    echo $LANG['FILTERED_PUSH'].'<br>';
    echo "<strong>$file".$LANG['NOT_FOUND'].'</strong>';
} else {
    if (isset($_GET['uri'])) {
        ?>
        <form action="response.php" method="post">
			<?php echo $LANG['ANNOTATOR_NAME']; ?>: <input type="text" size="20" name="annotator_name"/><br/>
			<?php echo $LANG['ANNOTATOR_EMAIL']; ?>: <input type="text" size="20" name="annotator_email"/><br/>
            <input type="radio" name="polarity" value="positive"/> <?php echo $LANG['AGREE']; ?>
			<input type="radio" name="polarity" value="neutral"/> <?php echo $LANG['NEUTRAL']; ?>
			<input type="radio" name="polarity" value="negative"/> <?php echo $LANG['DISAGREE']; ?><br/>
            <?php echo $LANG['OPINION_TEXT']; ?>: <input type="text" size="40" name="opinionText"/><br/>
            <?php echo $LANG['EVIDENCE']; ?>: <br/>
            <textarea name="evidence" rows="10" cols="35"></textarea><br/>
            <input type="hidden" name="annotationURI" value="<? echo $_GET['uri'] ?>"/>
            <input type="submit" value="Respond"/>
        </form>
    <?php
    } else if ($_SERVER['REQUEST_METHOD'] == "POST") {
        $fp = FPNetworkFactory::getNetworkFacade();

        $annotation = array();
        $annotation['target'] = array("annotationUri" => $_POST['annotationURI']);
        $annotation['body'] = array("polarity" => array("name" => $_POST['polarity']),
            "describesObject" => array("annotationUri" => $_POST['annotationURI']),
            "opinionText" => $_POST['opinionText']);
        $annotation['annotator_name'] = $_POST['annotator_name'];
        $annotation['annotator_email'] = $_POST['annotator_email'];
        $annotation['evidence'] = array("chars" => $_POST['evidence']);

        echo $fp->respond(AnnotationGenerator::responseAnnotation($annotation));

        echo "<script type='text/javascript'>";
        echo "window.close();";
        echo "</script>";
    }
}

?>