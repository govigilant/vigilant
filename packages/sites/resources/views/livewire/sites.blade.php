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
