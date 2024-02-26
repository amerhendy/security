<!-- select2 -->
    <?php
    $entity_model = $Amer->getModel();
    if(isset($field['orderby'])){
        $cond=$field['model']::orderby($field['orderby'])->get();
    }else{
        $cond=$field['model']::all();
    }
    $list=[];
    foreach ($cond as $key => $value) {
        if($value['ArName'] !== null){ $value['name']=$value['ArName'];}
        $list[$key]=[
            'id'=>$value['id'],
            'name'=>$value['name'],
            'guard_name'=>$value['guard_name'],
        ];
    }
    ?>
    <div class="row checklist">
    </div>
    @push('after_scripts')
        <script type="text/javascript">
            (function(){
                const alldata={{\Illuminate\Support\JS::from($list)}};
                const permissionFieldName="{{ $field['name'] }}";
                const oldDB={{\Illuminate\Support\JS::from($field["value"] ?? [])}};
                sortByGuard=function(target){
                    const old = [...new Set(oldDB.map(item => item.id))];
                    var selectedGuard=$('#guard_name').val();
                    var selectedData=alldata.filter(function(data){return data.guard_name == selectedGuard});
                    var notSelectedData=alldata.filter(function(data){return data.guard_name !== selectedGuard});
                    if(target == 'more'){
                        $('div[target=morecheckboxes]').parent().remove();
                    }
                    var checkall=$(`<div class="col-sm-4">
                            <div class="checkbox  py-1 list-group-item list-group-item-primary">
                                <label class="font-weight-normal">{{trans('AMER::actions.selectall')}}</label>
                                <input type="checkbox" class="control-input border" id="selectall">
                            </div>
                        </div>`);
                    if(target == 'target'){
                        $('.checklist').html(checkall);
                    }
                    if(target == 'target'){wantedData=selectedData;}else if(target == 'more'){wantedData=notSelectedData;}
                    $.each(wantedData,function(k,v){
                        var col4=$('<div class="col-sm-4"></div>');
                        var checkboxdiv=$(`<div class="checkbox py-1 list-group-item list-group-item-success">`);
                        var label=`<label class="font-weight-normal">`+v.name+`</label>`;
                        var checked='';
                        if(in_array(old,v.id)){
                            checked='checked'
                        }
                        var box=$(`<input type="checkbox" class="control-input border"
                      name="`+permissionFieldName+`[]"
                      value="`+v.id+`" `+checked+`>`);
                        $('.checklist').append(col4);
                        col4.html(checkboxdiv)
                        checkboxdiv.html(label)
                        checkboxdiv.append(box)
                    });
                    var loadMore=$(`<div class="col-sm-4">  
                            <div target="morecheckboxes" class="btn btn-success">
                                <label class="font-weight-normal">{{trans('SECLANG::permissionmanager.extra_permissions')}}</label>
                                </div>
                            </div>
                        </div>`);
                        if(target == 'target'){
                            $($( ".checklist" )).append(loadMore);
                            loadMore.click(function(){sortByGuard('more');});
                        }
                }
                sortByGuard('target');
                $('#guard_name').on('change',function(){sortByGuard('target');});
                $("#selectall").click(function(){$('input:checkbox').not(this).prop('checked', this.checked);});
            })(jQuery);
        </script>
        @endpush
