# Image Optimizer for Shopware 6

[![Latest Stable Version](https://img.shields.io/github/v/release/runelaenen/sw6-media-optimizer?color=lightblue&label=stable&logo=github)](//packagist.org/packages/runelaenen/sw6-media-optimizer)
[![Download plugin zip](https://img.shields.io/github/v/release/runelaenen/sw6-media-optimizer.svg?label=.zip%20download&logo=github)](https://github.com/runelaenen/sw6-media-optimizer/releases/latest)
[![Total Downloads](https://img.shields.io/packagist/dt/runelaenen/sw6-media-optimizer?label=packagist%20downloads&logo=composer)](//packagist.org/packages/runelaenen/sw6-media-optimizer)
[![GitHub Issues](https://img.shields.io/github/issues/runelaenen/sw6-media-optimizer?logo=github)](https://github.com/runelaenen/sw6-media-optimizer/issues)
[![GitHub Stars](https://img.shields.io/github/stars/runelaenen/sw6-media-optimizer?logo=github)](https://github.com/runelaenen/sw6-media-optimizer/stargazers)
[![License](https://poser.pugx.org/runelaenen/sw6-media-optimizer/license)](//packagist.org/packages/runelaenen/sw6-media-optimizer)

![Image Optimizer for Shopware 6](https://user-images.githubusercontent.com/3930922/102516043-b9b34780-408e-11eb-92f1-f9b0bdf70888.png)

Optimize image files using [spatie/image-optimizer](https://packagist.org/packages/spatie/image-optimizer). This package can optimize PNGs, JPGs, SVGs and GIFs by running them through a chain of various [image optimization tools](#optimization-tools). This Shopware plugin uses it's power to optimize the original files uploaded to your Shopware webshop. It doesn't touch any generated (thumbnail) files and thus will not have a lot of impact on the frontend of your website.

### Optimization tools

The package will use these optimizers if they are present on your system:

- [JpegOptim](http://freecode.com/projects/jpegoptim)
- [Optipng](http://optipng.sourceforge.net/)
- [Pngquant 2](https://pngquant.org/)
- [SVGO](https://github.com/svg/svgo)
- [Gifsicle](http://www.lcdf.org/gifsicle/)
- [cwebp](https://developers.google.com/speed/webp/docs/precompiled)

Please take a look at [the README of spatie/image-optimizer](https://packagist.org/packages/spatie/image-optimizer) to find out how to install these optimizers on your system.

## How to install
### Composer install (recommended)
```
composer require runelaenen/sw6-media-optimizer
bin/console plugin:refresh
bin/console plugin:install --activate RuneLaenenMediaOptimizer
```

You can now run the optimizer
```
bin/console rl:media:optimize
```

Tip: use the `--info` option to show all enabled optimizers.

#### Plugin update (composer)
```
composer update runelaenen/sw6-media-optimizer
bin/console plugin:update RuneLaenenMediaOptimizer
```

### .zip install
1. Download the latest RuneLaenenMediaOptimizer.zip from the [latest release](https://github.com/runelaenen/sw6-media-optimizer/releases/latest).
2. Upload the zip in the Shopware Administration
3. Install & Activate the plugin

#### Plugin update (zip)
1. Download the latest RuneLaenenMediaOptimizer.zip from the [latest release](https://github.com/runelaenen/sw6-media-optimizer/releases/latest).
2. Upload the zip in the Shopware Administration
3. Update the plugin
