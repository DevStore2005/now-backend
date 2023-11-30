@php
    $routeName = Route::currentRouteName();
 //   dd(request()->segment(1));
    $sideBarLinks = [
       'Admin Dashboard' => [
          [
             'isCollapsible' => false,
             'isActive' => $routeName == 'admin.dashboard',
             'route' => route('admin.dashboard', ['locale' => request()->query('locale')]),
             'icon' => 'dashboard',
             'title' => 'Dashboard'
          ],
          [
             'isCollapsible' => true,
             'icon' => 'miscellaneous_services',
             'title' => 'Services',
             'links' => [
                [
                   'title' => 'Main Services',
                   'isActive' => 'admin.services_list' == $routeName,
                   'route' => route('admin.services_list', ['locale' => request()->query('locale')]),
                ],
                [
                   'title' => 'Sub Services',
                   'isActive' => 'admin.sub_services_list' == $routeName,
                   'route' => route('admin.sub_services_list', ['locale' => request()->query('locale')]),
                ],
                [
                   'title' => 'Vehicle Types',
                   'isActive' => 'admin.vehicle.type.index' == $routeName,
                   'route' => route('admin.vehicle.type.index', ['locale' => request()->query('locale')]),
                ]
             ]
          ],
          [
             'isCollapsible' => false,
             'isActive' => $routeName == 'admin.users',
             'route' => route('admin.users', ['locale' => request()->query('locale')]),
             'icon' => 'people',
             'title' => 'Users'
          ],
          [
             'isCollapsible' => false,
             'isActive' => $routeName == 'admin.providers',
             'route' => route('admin.providers', ['locale' => request()->query('locale')]),
             'icon' => 'people',
             'title' => 'Providers'
          ],
               [
             'isCollapsible' => false,
             'isActive' => $routeName == 'admin.sliders.index',
             'route' => route('admin.sliders.index', ['locale' => request()->query('locale')]),
             'icon' => 'category',
             'title' => 'Sliders'
          ],
       ],
       'Restaurant and Grocery' => [
          [
             'isCollapsible' => false,
             'isActive' => $routeName == 'admin.countries.index',
             'route' => route('admin.countries.index', ['locale' => request()->query('locale')]),
             'icon' => 'paid',
             'title' => 'Countries'
          ],
 //           [
 //            'isCollapsible' => false,
 //            'isActive' => $routeName == 'admin.currency.index',
 //            'route' => route('admin.currency.index', ['locale' => request()->query('locale')]),
 //            'icon' => 'paid',
 //            'title' => 'Countries & Currencies'
 //         ],
          [
             'isCollapsible' => false,
             'isActive' => $routeName == 'admin.category.index',
             'route' => route('admin.category.index', ['locale' => request()->query('locale')]),
             'icon' => 'category',
             'title' => 'Categories'
          ],
//          [
//             'isCollapsible' => false,
//             'isActive' => $routeName == 'admin.restaurants',
//             'route' => route('admin.restaurants', ['locale' => request()->query('locale')]),
//             'icon' => 'restaurant',
//             'title' => 'Restaurants'
//          ],
//          [
//             'isCollapsible' => false,
//             'isActive' => $routeName == 'admin.grocery_stores',
//             'route' => route('admin.grocery_stores', ['locale' => request()->query('locale')]),
//             'icon' => 'store',
//             'title' => 'Grocry Stores'
//          ],
       ],
       'Blogs' => [
          [
             'isCollapsible' => false,
             'isActive' => $routeName == 'admin.category.index.blog',
             'route' => route('admin.category.index.blog', ['locale' => request()->query('locale')]),
             'icon' => 'category',
             'title' => 'Category'
          ],
          [
             'isCollapsible' => false,
             'isActive' => $routeName == 'admin.blog.index',
             'route' => route('admin.blog.index', ['locale' => request()->query('locale')]),
             'icon' => 'article',
             'title' => 'Blogs'
          ],
          [
             'isCollapsible' => false,
             'isActive' => $routeName == 'admin.blog.create',
             'route' => route('admin.blog.create', ['locale' => request()->query('locale')]),
             'icon' => 'create',
             'title' => 'Create Blog'
          ],
       ],
       'Articles' => [
          [
             'isCollapsible' => false,
             'isActive' => 'admin.article.index' == $routeName,
             'route' => route('admin.article.index', ['locale' => request()->query('locale')]),
             'icon' => 'list',
             'title' => 'Article List',
          ],
          [
             'isCollapsible' => false,
             'isActive' => 'admin.article.create' == $routeName,
             'route' => route('admin.article.create', ['locale' => request()->query('locale')]),
             'icon' => 'create',
             'title' => 'Create Article',
          ]
       ],
       'Links' => [
          [
             'isCollapsible' => false,
             'isActive' => $routeName == 'admin.link.index',
             'route' => route('admin.link.index', ['locale' => request()->query('locale')]),
             'icon' => 'link',
             'title' => 'Links'
          ],
          [
             'isCollapsible' => false,
             'isActive' => $routeName == 'admin.link.social',
             'route' => route('admin.link.social', ['locale' => request()->query('locale')]),
             'icon' => 'link',
             'title' => 'Social Links'
          ],
          [
             'isCollapsible' => false,
             'isActive' => $routeName == 'admin.link.blog',
             'route' => route('admin.link.blog', ['locale' => request()->query('locale')]),
             'icon' => 'link',
             'title' => 'Blog Links'
          ],
       ],
       'Pages' => [
          [
             'isCollapsible' => false,
             'isActive' => $routeName =='admin.page.index',
             'route' => route('admin.page.index', ['locale' => request()->query('locale')]),
             'icon' => 'link',
             'title' => 'Page List'
          ],
          [
             'isCollapsible' => false,
             'isActive' => $routeName =='admin.page.create',
             'route' => route('admin.page.create', ['locale' => request()->query('locale')]),
             'icon' => 'link',
             'title' => 'Create new Page'
          ],
       ],
       'Manage Seo' => [
           [
              'isCollapsible' => false,
              'isActive' => $routeName =='admin.seos.index',
              'route' => route('admin.seos.index', ['locale' => request()->query('locale')]),
              'icon' => 'link',
              'title' => 'Seo List'
           ],
       ],
       'Chat Module' => [
          [
             'isCollapsible' => false,
             'isActive' => $routeName == 'admin.chat.index',
             'route' => route('admin.chat.index', ['locale' => request()->query('locale')]),
             'icon' => 'message',
             'title' => "Chat"
          ],

       ],
       "FAQ's" => [
          [
             'isCollapsible' => false,
             'isActive' => $routeName == 'admin.faq.index',
             'route' => route('admin.faq.index', ['locale' => request()->query('locale')]),
             'icon' => 'question_answer',
             'title' => 'FAQs'
          ],
          [
             'isCollapsible' => false,
             'isActive' => $routeName == 'admin.faq.create',
             'route' => route('admin.faq.create', ['locale' => request()->query('locale')]),
             'icon' => 'create',
             'title' => 'Create FAQ'
          ],
       ],
       'Lead Plans' => [
          [
             'isCollapsible' => false,
             'isActive' => $routeName == 'admin.plan.index',
             'route' => route('admin.plan.index', ['locale' => request()->query('locale')]),
             'icon' => 'list',
             'title' => 'Plans'
          ],
       ],
       'Settings' => [
          [
             'isCollapsible' => false,
             'isActive' => $routeName == 'admin.commissions.index',
             'route' => route('admin.commissions.index', ['locale' => request()->query('locale')]),
             'icon' => 'money',
             'title' => 'Commissions'
          ]
       ],
       'Front page' => [
          [
             'isCollapsible' => false,
             'isActive' => $routeName == 'admin.front-pages.index',
             'route' => route('admin.front-pages.index', ['locale' => request()->query('locale')]),
             'icon' => 'home',
             'title' => 'Front page'
          ],
            [
             'isCollapsible' => false,
             'isActive' => $routeName == 'admin.help-pages.index',
             'route' => route('admin.help-pages.index', ['locale' => request()->query('locale')]),
             'icon' => 'home',
             'title' => 'Help page'
          ],
          [
             'isCollapsible' => false,
             'isActive' => $routeName == 'admin.front-pages.partner',
             'route' => route('admin.front-pages.partner', ['locale' => request()->query('locale')]),
             'icon' => 'people',
             'title' => 'Partners'
          ]
       ],
       'Payment Method' => [
          [
             'isCollapsible' => false,
             'isActive' => $routeName == 'admin.payment-method.index',
             'route' => route('admin.payment-method.index', ['locale' => request()->query('locale')]),
             'icon' => 'payment',
             'title' => 'Payment Method'
          ]
       ],
    ];
@endphp
<ul class="nav flex-column tw-none">
    @foreach ($sideBarLinks as $key => $links)
        <li class="nav-item active">
            <span class="nav-link">{{$key}}</span>
        </li>
        @foreach ($links as $link)
            @if ($link['isCollapsible'])
                <li class="nav-item">
                    <a class="nav-link" data-toggle="collapse" href="#collapse1" aria-expanded="true">
                        <i class="material-icons">{!! $link['icon'] !!}</i>
                        <span class="menudrop_icon">{!! $link['title'] !!}</span>
                    </a>
                    <div id="collapse1" class="collapse in">
                        @foreach ($link['links'] as $subLink)
                            <a class="nav-link {{$subLink['isActive']}}"
                               href="{{$subLink['route']}}"><span>{{$subLink['title']}}</span></a>
                        @endforeach
                    </div>
                </li>
            @else
                <li class="nav-item">
                    <a class="nav-link {{ $link['isActive'] ? 'active here' : '' }}" href="{{ $link['route'] }}">
                        <i class="material-icons">{{ $link['icon'] }}</i>
                        <span>{{ $link['title'] }}</span>
                    </a>
                </li>
            @endif
        @endforeach
    @endforeach
</ul>

<script>
   $(document).ready(function(){
      $('.here')[0]?.scrollIntoView({
         behavior: 'smooth'
      });
   });
</script>
