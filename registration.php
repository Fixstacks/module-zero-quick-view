<?php
/**
 * Copyright © 2026 Roman Pasichnyk
 * SPDX-License-Identifier: OSL-3.0 OR AFL-3.0
 *
 * Zero_QuickView: "Quick view" on product cards opens the product in a
 * dialog (photo, price, stock, summary, Add to Cart) without leaving the
 * list. The theme's <dialog> and HTMX, no script of its own.
 */

use Magento\Framework\Component\ComponentRegistrar;

ComponentRegistrar::register(ComponentRegistrar::MODULE, 'Zero_QuickView', __DIR__);
