<?php
require_once(dirname(__FILE__) . '/../classes/arbiter.php');
require_once(dirname(__FILE__) . '/../classes/configuration.php');

$configuration = &Configuration::instance();
if (! $configuration->getRepositoryPath()) {
    include(dirname(__FILE__) . '/installer.php');
    exit();
}
$arbiter = &new Arbiter($configuration);
$arbiter->handleUpload($_POST, $_FILES);

?><html>
<link rel="stylesheet" href="styles/arbiter.css">
<head><title>Arbiter Server</title></head>
<body>
    <h1>Arbiter Server</h1>
    <div id="content">
        <?php
            $titles = $arbiter->getAllTitles();
            if ($titles) {
                foreach ($titles as $title) {
        ?>
                <div class="document">
                    <table cellpadding="6" cellspacing="0">
                        <tr>
                            <th colspan="4" class="rtf">
                                <a href="download.php?d=<? echo urlencode($title); ?>"><?php echo $title; ?></a>
                            </th>
                        </tr>
                    </table>
                </div>
        <?php
                }
            } else {
        ?>
            <div class="message"><p>Waiting for first document...</p></div>
        <?php
            }
        ?>
	<div id="upload-form">	
        <h3>Upload A Requirements Document:</h3>
            <form id="upload" enctype="multipart/form-data" method="post">
                <?php
                    if ($arbiter->getLastUploadError()) {
                ?>
                    <div class="message">
                        <p><strong><? echo $arbiter->getLastUploadError(); ?></strong></p>
                    </div>
                <?php
                    }
                ?>
                <input type="hidden" name="MAX_FILE_SIZE" value="30000">
                <p><input type="file" name="document" size="38"></p>
                <p>The document must be saved in <em class="rtf">.rtf</em> format</p>
                <input type="submit" class="submit" value="Upload Document &raquo;">
            </form>
        </div>
    </div>
</body>
</html>