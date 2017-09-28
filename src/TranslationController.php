<?php

namespace Encore\Admin\Translation;

use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Widgets\Box;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TranslationController
{
    /**
     * Index interface.
     *
     * @return Content
     */
    public function index(Request $request)
    {
        if ($group = $request->get('export')) {
            $translation = new Translation();

            $translation->exportTranslations($group);

            return response()->json([
                'status'  => true,
                'message' => trans('admin.export_success'),
            ]);
        }

        return Admin::content(function (Content $content) {
            $content->header('Translations');
            $content->description('Translation list.');

            $content->body($this->grid());
        });
    }

    /**
     * Edit interface.
     *
     * @param $id
     *
     * @return Content
     */
    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {
            $content->header('Edit translations');

            list($group, $key) = explode('.', $id);

            $locales = $this->getLocaleOptions();

            $translations = TranslationModel::where(compact('group', 'key'))->get();

            $content->body(view('laravel-admin-translations::edit', compact('translations', 'locales', 'id', 'group', 'key')));
        });
    }

    public function update($id, Request $request)
    {
        list($group, $key) = explode('.', $id);

        $translations = TranslationModel::where(compact('group', 'key'))->get();

        foreach ($request->input('values') as $id => $item) {
            if (is_int($id)) {
                $model = $translations->find($id);
            } else {
                $model = new TranslationModel();
                $model->group = $group;
                $model->key = $key;
            }

            if (empty($model)) {
                continue;
            }

            if ($item['_remove_'] == 1) {
                $model->delete();
                continue;
            }

            $model->locale = $item['locale'];
            $model->value = $item['value'];

            $model->save();
        }

        admin_toastr(trans('admin.update_succeeded'));

        return redirect(route('translations.index'));
    }

    /**
     * Create interface.
     *
     * @return Content
     */
    public function create()
    {
        return Admin::content(function (Content $content) {
            $content->header('Create new translation');

            $form = new \Encore\Admin\Widgets\Form(compact('group', 'key'));

            $form->select('group')->options($this->getGroupOptions());
            $form->text('key');

            $form->divider();

            $form->hasMany('locales', function (Form\NestedForm $form) {
                $form->select('locale', 'Locale')->options($this->getLocaleOptions());
                $form->textarea('value', 'Text');
            });

            $form->action(route('translations.store'));

            $content->body(new Box('', $form));
        });
    }

    public function store(Request $request)
    {
        $translation = new Translation();

        foreach ($request->input('locales') as $item) {
            $translation->importTranslation(
                $request->input('key'),
                $item['value'],
                $item['locale'],
                $request->input('group')
            );
        }

        admin_toastr(trans('admin.save_succeeded'));

        return redirect(route('translations.index'));
    }

    public function grid()
    {
        return Admin::grid(TranslationModel::class, function (Grid $grid) {
            $grid->model()->groupBy(['group', 'key'])->select(['id', 'group', 'key', DB::raw('GROUP_CONCAT(status SEPARATOR \',\') as status'), DB::raw('GROUP_CONCAT(DISTINCT CONCAT(locale,\'###\',value) ORDER BY locale ASC SEPARATOR \'|||\') as value'), 'created_at', 'updated_at']);

            $grid->column('usage')->display(function () {
                return "<code>trans('{$this->group}.{$this->key}')</code>";
            });

            $grid->value('Locale : Text')->display(function ($value) {
                $html = '<dl class="dl-horizontal" style="margin: 0px;">';

                foreach (explode('|||', $value) as $value) {
                    list($locale, $value) = explode('###', $value);
                    $html .= "<dt style='width: 45px;'>$locale:</dt><dd style='margin-left: 50px;margin-bottom: 5px;'><em>$value</em></dd>";
                }

                return $html.'</dl>';
            });

            $grid->group();
            $grid->key();

            $grid->status()->display(function ($status) {
                $status = explode(',', $status);

                $changed = in_array(TranslationModel::STATUS_CHANGED, $status);

                return $changed ? '<span class="label label-danger">changed</span>'
                    : '<span class="label label-success">saved</span>';
            });

            $grid->updated_at();

            $grid->filter(function (Grid\Filter $filter) {
                $filter->disableIdFilter();

                $filter->equal('group')->select($this->getGroupOptions());
                $filter->equal('key');
                $filter->equal('locale')->select($this->getLocaleOptions());
            });

            $grid->actions(function (Grid\Displayers\Actions $actions) {
                $actions->setKey($this->row->group.'.'.$this->row->key);
            });

            $grid->tools(function (Grid\Tools $tools) {
                $tools->append(new ExportButton());
            });

            $grid->disableExport();
            $grid->disableRowSelector();
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(TranslationModel::class, function (Form $form) {
            $form->display('id', 'ID');

            $form->text('group');
            $form->text('key');

            $form->divider();

            $form->select('locale')->options($this->getLocaleOptions())->default(config('app.locale'));
            $form->textarea('value');

            $form->divider();

            $form->select('locale')->options($this->getLocaleOptions())->default(config('app.locale'));
            $form->textarea('value');

            $form->divider();

            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }

    public function destroy($id)
    {
        list($group, $key) = explode('.', $id);

        if (TranslationModel::where(compact('group', 'key'))->delete()) {
            return response()->json([
                'status'  => true,
                'message' => trans('admin.delete_succeeded'),
            ]);
        } else {
            return response()->json([
                'status'  => false,
                'message' => trans('admin.delete_failed'),
            ]);
        }
    }

    protected function getLocaleOptions()
    {
        $locales = Translation::getLocales();

        return array_combine($locales, $locales);
    }

    protected function getGroupOptions()
    {
        $groups = Translation::getGroups();

        return array_combine($groups, $groups);
    }
}
