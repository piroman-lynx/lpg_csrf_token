<html>
    <body>
        <?php echo $loading_text; ?>
        <script type='text/javascript'> <?php /** check img and other left src tags **/ ?>
            if (parent.document.location.href == document.location.href){ <?php /** check iframe **/ ?>
                document.location.href='<?php echo $url; ?>';
            }
        </script>
    </body>
</html>