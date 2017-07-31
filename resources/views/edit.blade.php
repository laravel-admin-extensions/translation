<script data-exec-on-popstate>

    $(function () {

        $('.locales-select').select2();

        var index = 0;
        $('.has-many-locales').on('click', '.add', function () {

            var tpl = $('template.locales-tpl');

            index++;

            var template = tpl.html().replace(/__LA_KEY__/g, index);
            $('.has-many-locales-forms').append(template);

            $('.locales-select').select2();
        });

        $('.has-many-locales').on('click', '.remove', function () {
            $(this).closest('.has-many-locales-form').hide();
            $(this).closest('.has-many-locales-form').find('.fom-removed').val(1);
        });
    });

</script>
<div class="row">
    <div class="col-md-12">
        <div  class="box">
            <div class="box-header with-border">
                <h3 class="box-title">trans('{{$id}}')</h3>
                <div class="box-tools">
                    <div class="btn-group pull-right" style="margin-right: 10px">
                        <a href="{{ route('translations.index') }}" class="btn btn-sm btn-default"><i class="fa fa-list"></i>&nbsp;{{ trans('admin.list') }}</a>
                    </div>
                </div>
            </div><!-- /.box-header -->
            <div class="box-body" style="display: block;">
                <form method="POST" action="{{ route('translations.update', $id) }}" class="form-horizontal" accept-charset="UTF-8" pjax-container="1">
                    <div class="box-body fields-group">
                        <div class="form-group  ">
                            <label for="group" class="col-sm-2 control-label">Group</label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-pencil"></i></span>
                                    <input readonly type="text" value="{{ $group }}" name="group" class="form-control"/>
                                </div>
                            </div>
                        </div>
                        <div class="form-group  ">
                            <label for="key" class="col-sm-2 control-label">Key</label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-pencil"></i></span>
                                    <input readonly type="text" value="{{ $key }}" name="key" class="form-control"/>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-2"><h4 class="pull-right">Values</h4></div>
                            <div class="col-md-8"></div>
                        </div>
                        <hr style="margin-top: 0px;">

                        <div class="has-many-locales">
                            <div class="has-many-locales-forms">

                                @foreach($translations as $translation)
                                <div class="has-many-locales-form">
                                    <div class="form-group">
                                        <label for="locales[{{$translation->id}}][locale]" class="col-sm-2 control-label">Locale</label>
                                        <div class="col-sm-8">
                                            <input type="hidden" name="values[{{$translation->id}}][locale]"/>
                                            <select class="form-control locales-select" style="width: 100%;" name="values[{{$translation->id}}][locale]"  >
                                                @foreach($locales as $locale)
                                                    <option value="{{$locale}}" {{ ($translation->locale == $locale) ? 'selected' : '' }}>{{$locale}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group  ">
                                        <label for="locales[{{$translation->id}}][value]" class="col-sm-2 control-label">Text</label>
                                        <div class="col-sm-8">
                                            <textarea name="values[{{$translation->id}}][value]" class="form-control" rows="3">{{$translation->value}}</textarea>
                                        </div>
                                    </div>

                                    <input type="hidden" name="values[{{$translation->id}}][_remove_]" value="0" class="fom-removed"  />

                                    <div class="form-group">
                                        <label  class="col-sm-2 control-label"></label>
                                        <div class="col-sm-8">
                                            <div class="remove btn btn-warning btn-sm pull-right"><i class="fa fa-trash"></i>&nbsp;{{ trans('admin.remove') }}</div>
                                        </div>
                                    </div>
                                    <hr>
                                </div>
                                @endforeach

                            </div>

                            <div class="form-group">
                                <label  class="col-sm-2 control-label"></label>
                                <div class="col-sm-8">
                                    <div class="add btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;{{ trans('admin.new') }}</div>
                                </div>
                            </div>

                        </div>

                        <input type="hidden" name="_method" value="PUT" class="_method"  />

                    </div>

                    <!-- /.box-body -->
                    <div class="box-footer">
                        {{ csrf_field() }}
                        <div class="col-sm-2">
                        </div>
                        <div class="col-sm-2">
                            <div class="btn-group pull-left">
                                <button type="reset" class="btn btn-warning pull-right">{{ trans('admin.reset') }}</button>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="btn-group pull-right">
                                <button type="submit" class="btn btn-info pull-right">{{ trans('admin.submit') }}</button>
                            </div>
                        </div>

                    </div>
                </form>
            </div><!-- /.box-body -->
        </div>
    </div>
</div>

<template class="locales-tpl">
    <div class="has-many-locales-form fields-group">

        <div class="form-group">
            <label class="col-sm-2 control-label">Locale</label>
            <div class="col-sm-8">
                <input type="hidden" name="values[new___LA_KEY__][locale]"/>
                <select class="form-control locales-select" style="width: 100%;" name="values[new___LA_KEY__][locale]"  >
                    @foreach($locales as $locale)
                        <option value="{{$locale}}">{{$locale}}</option>
                    @endforeach
                </select>
            </div>
        </div><div class="form-group">
            <label class="col-sm-2 control-label">Text</label>
            <div class="col-sm-8">
                <textarea name="values[new___LA_KEY__][value]" class="form-control" rows="3"></textarea>
            </div>
        </div>
        <input type="hidden" name="values[new___LA_KEY__][_remove_]" value="0" class="locales fom-removed"/>

        <div class="form-group">
            <label class="col-sm-2 control-label"></label>
            <div class="col-sm-8">
                <div class="remove btn btn-warning btn-sm pull-right"><i class="fa fa-trash"></i>&nbsp;{{ trans('admin.remove') }}</div>
            </div>
        </div>
        <hr>
    </div>
</template>