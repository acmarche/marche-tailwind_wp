<?php

namespace AcMarche\MarcheTail;

use AcMarche\MarcheTail\Lib\Menu;
use AcMarche\MarcheTail\Lib\Twig;

$menu = new Menu();
$items = $menu->getAllItems();
wp_footer();
Twig::rendPage(
    '@MarcheBe/footer/_footer.html.twig',
    [
        'items' => $items,
    ]
);
echo '
</body>
</html>';