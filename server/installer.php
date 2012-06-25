<?php
    require_once(dirname(__FILE__) . '/../classes/installer.php');
    
    $installer = new Installer();
    if (isset($_REQUEST['create'])) {
        $installer->install($_REQUEST['location']);
    }
    if (isset($_REQUEST['remove'])) {
        $installer->uninstall($_REQUEST['confirmation']);
    }
?><html>
    <head><title>Arbiter Installer</title></head>
    <body>
        <h1>Arbiter installer</h1>
        <h2>Current repository...</h2>
        <p>
            <?php
                if ($installer->isInstalled()) {
            ?>
                    <a href="index.php">Repository location is <?php echo $installer->getRepositoryPath() ?></a>
            <?php
                } else {
            ?>
                    You need to create a new repository to begin.
            <?php
                }
            ?>
        </p>
        <?php
            if ($installer->isInstalled()) {
        ?>
                <h2>Uninstall the repository...</h2>
                <p>
                    <em>Warning: this will delete all of your requirements documents!</em>
                    <br />
                    <form>
                        Confirm location:
                        <input type="text" name="confirmation" value="" />
                        <br />
                        <input type="submit" name="remove" value="Remove repository" />
                    </form>
                </p>
        <?php
            } else {
        ?>
                <h2>Create a new Repository...</h2>
                <p>
                    <form>
                        New repository directory:
                        <input type="text" name="location" value="" />
                        <br />
                        <em>Please note that your web server must be able to write to this directory.</em>
                        <br />
                        <input type="submit" name="create" value="Create repository" />
                    </form>
                </p>
        <?php
            }
        ?>
    </body>
</html>