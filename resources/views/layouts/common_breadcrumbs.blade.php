<!-- Content Header (Page header) -->
<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">{{ $heading }}</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                @if(isset($breadcrumbs) && !empty($breadcrumbs))
                  @for($i = 0; $i < count($breadcrumbs); $i++)

                    @if(isset($breadcrumbs[$i]['url']) && !empty($breadcrumbs[$i]['url']))
                      <a href="{{ $breadcrumbs[$i]['url'] }}">
                    @endif
                      {{ $breadcrumbs[$i]['name'] }}

                        @if(isset($breadcrumbs[$i]['url']) && !empty($breadcrumbs[$i]['url']))
                        </a> &nbsp/ 
                        @endif
                  @endfor
              @endif
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->