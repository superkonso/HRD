<?php 
  
  use SIMRS\Menuitem;
  use SIMRS\Menu;
  use App\CustomCollection;
  use Illuminate\Database\Eloquent\Model;

  use SIMRS\Helpers\accessList;

  $menu     = Menu::orderBy('TMenu_Kode', 'asc')->get();
  $menuitem = Menuitem::orderBy('TMenu_Kode', 'ASC')->orderBy('TMenuItem_Item', 'ASC')->get();
  $Hmenu    = Menuitem::where('TMenuItem_Jenis', '=', 'H')->orderBy('TMenu_Kode', 'ASC')->orderBy('TMenuItem_Item', 'ASC')->get();

  // dd($Hmenu);

?>

  <aside class="main-sidebar">

    <section class="sidebar">
      <ul class="sidebar-menu">

        @foreach($menu as $listmenu)

            <?php 
              $aksesMenu = accessList::checkMenuAccess($listmenu->TMenu_Kode, Auth::user()->id);
            ?>

            @if($aksesMenu == 1 OR $aksesMenu == '1')

              <li class="treeview">
                <a href="#">
                  @if(empty($listmenu->TMenu_Logo) OR $listmenu->TMenu_Logo == '') <img src="{!! asset('images/menu/menu-icon.png') !!}" height="20px" width="20px"></img> @else <img src="{!! asset('images/menu') !!}/{{$listmenu->TMenu_Logo}}" height="20px" width="20px"></img> @endif <span>&nbsp;&nbsp;{{$listmenu->TMenu_Nama}}</span>
                  <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                  @foreach($Hmenu as $headermenu)
                    @if($listmenu->TMenu_Kode == $headermenu->TMenu_Kode)

                      <?php 
                        $aksesH = accessList::checkUserAccess($listmenu->TMenu_Kode, $headermenu->TMenuItem_Item, Auth::user()->id);
                      ?>

                      @if($aksesH == 1 OR $aksesH == '1')

                        <li>
                          <a href="#"> @if(empty($headermenu->TMenu_Logo) OR $headermenu->TMenu_Logo == '') <img src="{!! asset('images/menu/list-menu-icon-small.png') !!}" height="20px" width="20px"></img> @else <img src="{!! asset('images/menu') !!}/{{$headermenu->TMenu_Logo}}"></img> @endif {{$headermenu->TMenuItem_Nama}} <i class="fa fa-angle-left pull-right"></i></a>
                          <ul class="treeview-menu" style="white-space: normal;"> 

                            @foreach($menuitem as $listmenuitem)
                              @if($listmenuitem->TMenu_Kode == $headermenu->TMenu_Kode && substr($listmenuitem->TMenuItem_Item,0,1) == substr($headermenu->TMenuItem_Item,0,1) && $listmenuitem->TMenuItem_Jenis == 'M')

                                <?php 
                                  $akses = accessList::checkUserAccess($listmenu->TMenu_Kode, $listmenuitem->TMenuItem_Item, Auth::user()->id);
                                ?>

                                @if($akses == 1 OR $akses == '1')

                                <li>
                                  <a href="{{$listmenuitem->TMenuItem_Link}}"> @if(empty($listmenuitem->TMenu_Logo) OR $listmenuitem->TMenu_Logo == '') <i class="fa fa-arrow-circle-right"></i> @else <img src="{!! asset('images/menu') !!}/{{$listmenuitem->TMenu_Logo}}"></img> @endif {{$listmenuitem->TMenuItem_Nama}}</a>
                                </li>

                                @endif

                              @endif
                            @endforeach

                          </ul>
                       </li>
                      @endif

                    @endif

                  @endforeach

                </ul>
              </li>

            @endif

        @endforeach

      </ul>

    </section>

  </aside>
