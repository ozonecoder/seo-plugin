<?php namespace Ozc\SEO;

use Event;
use System\Classes\PluginBase;
use Cms\Classes\Page;
use Cms\Classes\Theme;
use System\Classes\PluginManager;
use System\Classes\SettingsManager;
use Ozc\SEO\classes\Helper;

class Plugin extends PluginBase{

	// DESCRIPTION
	public function pluginDetails(){
		return [
			'name'        => 'ozc SEO',
			'description' => 'SEO Extension',
			'icon'        => 'icon-search',
			'author'      => 'Ozonecoders | Pierre Otto',
			'homepage'      => 'https://www.ozonecoders.de'
		];
	}

	// COMPONENTS
	public function registerComponents(){
		return [
		    'Ozc\SEO\Components\SeoPage' => 'SeoPage',
		    'Ozc\SEO\Components\SeoPageStatic' => 'SeoPageStatic'
        ];
	}

	// REGISTER FIELDS
	public function register(){
		Event::listen('backend.form.extendFields', function($widget){

		    // static pages
            if (PluginManager::instance()->hasPlugin('RainLab.Pages') && $widget->model instanceof \RainLab\Pages\Classes\Page) {
                $widget->addFields(
                    [
                        'viewBag[seo_keywords]' => [
                        'label'   => 'Meta Keywords',
                        'type'    => 'textarea',
                        'tab'     => 'cms::lang.editor.meta',
                        'size'    => 'tiny',
                        'placeholder' => "hello"
                    ],
                        'viewBag[canonical_url]' => [
                        'label'   => 'Canonical URL',
                        'type'    => 'text',
                        'tab'     => 'SEO Options',
                        'span'    => 'left'
                    ],
                        'viewBag[redirect_url]' => [
                        'label'   => 'Redirect URL',
                        'type'    => 'text',
                        'tab'     => 'SEO Options',
                        'span'    => 'right'
                    ],
                        'viewBag[robot_index]' => [
                        'label'   => 'Robot Index',
                        'type'    => 'dropdown',
                        'tab'     => 'SEO Options',
                        'options' => $this->getIndexOptions(),
                        'default' => 'index',
                        'span'    => 'left'
                    ],
                        'viewBag[robot_follow]' => [
                        'label'   => 'Robot Follow',
                        'type'    => 'dropdown',
                        'tab'     => 'SEO Options',
                        'options' => $this->getFollowOptions(),
                        'default' => 'follow',
                        'span'    => 'right'
                    ],
                        'viewBag[image]' => [
                        'label'   => 'Open Graph Image',
                        'type'    => 'mediafinder',
                        'mode'    => 'image',
                        'tab'     => 'SEO Options',
                        'span'    => 'full'
                    ],
                    ],
                    'primary'
                );
            }
		    // cms pages
			if ($widget->model instanceof \Cms\Classes\Page){
                if (!($theme = Theme::getEditTheme()))
                    throw new ApplicationException(Lang::get('cms::lang.theme.edit.not_found'));

                $widget->addFields(
                    [
                        'settings[seo_keywords]' => [
                            'label'   => 'Meta Keywords',
                            'type'    => 'textarea',
                            'tab'     => 'cms::lang.editor.meta',
                            'size'    => 'tiny',
                            'placeholder' => "hello"
                        ],
                        'settings[canonical_url]' => [
                            'label'   => 'Canonical URL',
                            'type'    => 'text',
                            'tab'     => 'SEO Options',
                            'span'    => 'left'
                        ],
                        'settings[redirect_url]' => [
                            'label'   => 'Redirect URL',
                            'type'    => 'text',
                            'tab'     => 'SEO Options',
                            'span'    => 'right'

                        ],
                        'settings[robot_index]' => [
                            'label'   => 'Robot Index',
                            'type'    => 'dropdown',
                            'tab'     => 'SEO Options',
                            'options' => $this->getIndexOptions(),
                            'default' => 'index',
                            'span'    => 'left'
                        ],
                        'settings[robot_follow]' => [
                            'label'   => 'Robot Follow',
                            'type'    => 'dropdown',
                            'tab'     => 'SEO Options',
                            'options' => $this->getFollowOptions(),
                            'default' => 'follow',
                            'span'    => 'right'
                        ],
                        'settings[image]' => [
                            'label'   => 'Open Graph Image',
                            'type'    => 'mediafinder',
                            'mode'    => 'image',
                            'tab'     => 'SEO Options',
                            'span'    => 'full'
                        ],
                    ],
                    'primary'
                );
            }


		});
	}

	// OPTIONS
	public function registerSettings(){
		return [
			'settings' => [
				'label'       => 'SEO Extension',
				'description' => 'Configure SEO Extension',
                'category'    => 'Ozc Plugins',
				'icon'        => 'icon-search',
				'class'       => 'Ozc\SEO\Models\Settings',
				'order'       => 100
			]
		];
	}

	// MARKUPS
	public function registerMarkupTags(){
		return [
			'filters' => [
				'generateTitle' => [$this, 'generateTitle'],
				'generateCanonicalUrl' => [$this, 'generateCanonicalUrl'],
				'otherMetaTags' => [$this ,'otherMetaTags'],
				'generateOgTags' => [$this,'generateOgTags']
			]
		];
	}

	public function generateOgTags($post){
		$helper = new Helper();

		$ogMetaTags = $helper->generateOgMetaTags($post);
		return $ogMetaTags;
	}

	public function otherMetaTags(){
		$helper = new Helper();

		$otherMetaTags = $helper->otherMetaTags();
		return $otherMetaTags;
	}

	public function generateTitle($title){
		$helper = new Helper();
		$title = $helper->generateTitle($title);
		return $title;
	}

	public function generateCanonicalUrl($url){
		$helper = new Helper();
		$canonicalUrl = $helper->generateCanonicalUrl();
		return $canonicalUrl;
	}

	// OPTIONS PER FIELD
	private function getIndexOptions(){
		return ["index"=>"index","noindex"=>"noindex"];
	}

	private function getFollowOptions(){
		return ["follow"=>"follow","nofollow"=>"nofollow"];
	}
}

?>