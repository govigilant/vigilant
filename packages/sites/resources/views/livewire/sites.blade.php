<div>
    <x-slot name="header">
        <x-page-header title="Sites">
            <x-form.button :href="route('site.create')">
                @lang('Add Site')
            </x-form.button>
        </x-page-header>
    </x-slot>

    <x-listing.status.wrapper>
        @foreach($sites as $site)
           <x-listing.status.item :title="$site->url">

               <x-slot:subtitle>
                   <p class="truncate">Deploys from GitHub</p>
                   <svg viewBox="0 0 2 2" class="h-0.5 w-0.5 flex-none fill-gray-300">
                       <circle cx="1" cy="1" r="1"/>
                   </svg>
                   <p class="whitespace-nowrap">Initiated 1m 32s ago</p>
               </x-slot:subtitle>

               <div class="text-neutral-100">
                   latency grafiekje
               </div>

           </x-listing.status.item>
        @endforeach
    </x-listing.status.wrapper>

    {{ $sites->links() }}

</div>
