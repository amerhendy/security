<style>
    .permission-card {
      margin-bottom: 15px;
      border-radius: 8px;
      padding: 10px;
      background-color: #f8f9fa;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }
    .permission-card label {
      margin-left: 10px;
    }
    .permission-search-box {
      margin-bottom: 20px;
    }
  </style><!-- select2 -->
    <?php
    $entity_model = $Amer->getModel();
    if(isset($field['orderby'])){
        $cond=$field['model']::orderby($field['orderby'])->get();
    }else{
        $cond=$field['model']::all();
    }
    $list=[];
    foreach ($cond as $key => $value) {
        if($value['ar_name'] !== null){ $value['name']=$value['ar_name'];}
        $list[$key]=[
            'id'=>$value['id'],
            'name'=>$value['name'],
            'guard_name'=>$value['guard_name'],
        ];
    }
    $ids = [];
    foreach ($field["value"] as $key => $value) {
        if($value['ar_name'] !== null){ $value['name']=$value['ar_name'];}
        $ids[]=$value['id'];
    }
    ?>
    <div
        class="row checklist"
        data-init-function="ListPermissionInRoles"
        data-alldata='@json($list)'
        data-permissionFieldName="{{ $field['name'] }}"
        data-oldDb='@json($ids)'
        >
        <div class="row permission-search-box">
            <div class="col-sm-12">
              <input type="text" class="form-control" id="permissionSearch" placeholder="Search permissions...">
            </div>
          </div>

          <div id="permissionContainer" class="row row-cols-1 row-cols-md-2 g-4">
            <!-- صلاحيات سيتم إضافتها هنا من خلال جافا سكربت -->
          </div>
        </div>
    </div>
    @push('after_scripts')
    @loadScriptOnce('js/Security/roles.js')
        <script type="text/javascript">
                const alldata={{\Illuminate\Support\JS::from($list)}};
                const permissionFieldName="{{ $field['name'] }}";
                const oldDB={{\Illuminate\Support\JS::from($field["value"] ?? [])}};
                jstrans['permissionmanager']={{\Illuminate\Support\JS::from(trans('SECLANG::permissionmanager'))}};
//                jstrans['crud']={{\Illuminate\Support\JS::from(trans('AMERLANG::crud'))}};
        </script>
        @endpush
