/*
 * Velan Info Services India Pvt Ltd.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://store.velanapps.com/License.txt
 *
  /***************************************
 *         MAGENTO EDITION USAGE NOTICE *
 * *************************************** */
/* This package designed for Magento COMMUNITY edition
 * Velan Info Services does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * Velan Info Services does not provide extension support in case of
 * incorrect edition usage.
  /***************************************
 *         DISCLAIMER   *
 * *************************************** */
/* Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future.
 * ****************************************************
 * @category   velanapps
 * @package    SD
 * @author     Velan Team 
 * @copyright  Copyright (c) 2013 - 2014 Velan Info Services India Pvt Ltd. (http://www.velanapps.com)
 * @license    http://store.velanapps.com/License.txt
 */
// based on http://pastebin.com/0j0h2Lws
SeasonalDecorations = Class.create({
    initialize: function(options) {
        this.options = {
            flakes: 35,
            color: ['#e9f0e0', '#d1e8f0', '#bbb', '#ddd', '#fff'],
            text: '*',
            speed: 2,
            size: { 'max': 35, 'min': 8 }
        };
        Object.extend(this.options, options || {});
        this.elements = [];
        for (var i = 0; i <= this.options.flakes; i++) {
            this.elements[i] = new Element('span').
            setStyle({
                position: 'absolute',
                top: '0px',
                cursor: 'default',
                background: 'transparent'
            }).
            update(this.options.text);
			
        }
        document.observe('dom:loaded', this.onDomLoad.bindAsEventListener(this));
    },
    onDomLoad: function() {
        var viewport = document.viewport.getDimensions();
        this.container = new Element('div', {
            'id': 'SeasonalDecorations'
        });
        this.container.setStyle({
            oveflow: 'hidden'
        });
        (document.getElementsByTagName('body')[0]).appendChild(this.container);
        this.elements.each(function(item) {
            this.container.appendChild(item);
            item.size = (this.random(this.options.size.max, this.options.size.min));
            Object.extend(item, {
                cords: 0,
                across: (Math.random() * 15),
                horizontal: (0.03 + Math.random() / 10),
                sink: (this.options.speed * 0.5),
                posx: (this.random(viewport.width - item.size)),
                posy: (this.random(document.body.clientHeight - item.size))
            });
            item.setStyle({
                fontSize: item.size + 'px',
                color: this.options.color[this.random(this.options.color.length)],
                left: item.posx + 'px',
                top: item.posy + 'px',
                zIndex: '1000'
            });
        },
        this);
        this.start();
    },
    move: function() {
        var viewport = document.viewport.getDimensions();
        this.elements.each(function(item) {
            item.cords += item.horizontal;
            item.posy += item.sink;
            item.setStyle({
                top: item.posy + 'px',
                left: (item.posx + item.across * Math.sin(item.cords)) + 'px'
            });
            if (item.posy >= document.body.clientHeight - item.size / 2 || parseInt(item.getStyle('left')) > (viewport.width - 3 * item.across)) {
                item.posx = this.random(viewport.width - item.size);
                item.posy = -item.size;
            }
        },
        this);
    },
    random: function(max, min) {
        if (!min) {
            return Math.floor(Math.random() * max);
        }
        return Math.floor((Math.random() * (max - min + 1)) + min);
    },
    start: function() {
        this.pe = new PeriodicalExecuter(this.move.bindAsEventListener(this), 0.05);
    },
    stop: function() {
        this.pe.stop();
    }
});
