<?php

namespace App\View\Components\Blocks;

use Illuminate\View\Component;

class Infographic extends Component
{
    public $media;

    public $transcript;

    public $embedCode;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(array $data)
    {
        $this->media = resolve(config('filament-curator.model'))->where('id', $data['image'])->first();
        $this->transcript = $data['transcript'];

        $this->embedCode = json_encode('<a href="'.url()->current()."\">\r\n\t<img src=\"".$this->media->url.'" alt="'.$this->media->alt.'" width="'.$this->media->width.'" height="'.$this->media->height."\">\r\n</a>\r\n<a href=\"".config('app.url').'">By '.config('brand.source', config('app.name')).'</a>');
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.blocks.infographic');
    }
}
