<?php

namespace Encore\Admin\Translation;

use Encore\Admin\Facades\Admin;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Facades\DB;

class ExportButton implements Renderable
{
    protected function getGroups()
    {
        $groups = TranslationModel::select(DB::raw('DISTINCT `group`'))->get()->toarray();

        return array_flatten($groups);
    }

    protected function setupScript()
    {
        $script = <<<'SCRIPT'

$('._export').click(function () {
    $.ajax({
        url: $(this).attr('href'),
        type: "GET",
        success: function (data) {
            $.pjax.reload('#pjax-container');
            toastr.success(data.message);
        }
    });

    return false;
});

SCRIPT;

        Admin::script($script);
    }

    /**
     * Render Export button.
     *
     * @return string
     */
    public function render()
    {
        $this->setupScript();

        $export = trans('admin.export');

        $links = '';

        foreach ($this->getGroups() as $group) {
            $action = route('translations.index', ['export' => $group]);
            $links .= "<li><a href=\"$action\" target=\"_blank\" class='_export'>$group</a></li>";
        }

        return <<<EOT

<div class="btn-group" style="margin-right: 10px">
    <a class="btn btn-sm btn-twitter"><i class="fa fa-download"></i> {$export}</a>
    <button type="button" class="btn btn-sm btn-twitter dropdown-toggle" data-toggle="dropdown">
        <span class="caret"></span>
        <span class="sr-only">Toggle Dropdown</span>
    </button>
    <ul class="dropdown-menu" role="menu">
        $links
    </ul>
</div>
&nbsp;&nbsp;

EOT;
    }

    public function __toString()
    {
        return $this->render();
    }
}
