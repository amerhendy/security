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
    <div
        class="row checklist"
        data-init-function="checkListPermissionINIT"
        data-alldata='@json($list)'
        data-permissionFieldName="{{ $field['name'] }}"
        data-oldDb='@json($field["value"] ?? [])'
        >
    </div>
    @push('after_scripts')
    @loadScriptOnce('js/Security/roles.js')
        <script type="text/javascript">
                const alldata={{\Illuminate\Support\JS::from($list)}};
                const permissionFieldName="{{ $field['name'] }}";
                const oldDB={{\Illuminate\Support\JS::from($field["value"] ?? [])}};
                jstrans['permissionmanager']={{\Illuminate\Support\JS::from(trans('SECLANG::permissionmanager'))}};
        </script>
        @endpush
