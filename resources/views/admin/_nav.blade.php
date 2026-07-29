@php $active = $active ?? request()->route()->getName(); @endphp

<nav class="flex gap-1 border-b border-line mb-8">
    @hasanyrole('owner|admin')
        <a href="{{ route('admin.dashboard') }}"
           class="px-4 py-2.5 text-sm font-medium border-b-2 -mb-px transition-colors {{ $active === 'admin.dashboard' ? 'border-pitch-700 text-pitch-800' : 'border-transparent text-ink/50 hover:text-ink' }}">
            {{ __('Overview') }}
        </a>
        <a href="{{ route('admin.earnings') }}"
           class="px-4 py-2.5 text-sm font-medium border-b-2 -mb-px transition-colors {{ $active === 'admin.earnings' ? 'border-pitch-700 text-pitch-800' : 'border-transparent text-ink/50 hover:text-ink' }}">
            {{ __('Earnings') }}
        </a>
        <a href="{{ route('admin.payouts') }}"
           class="px-4 py-2.5 text-sm font-medium border-b-2 -mb-px transition-colors {{ $active === 'admin.payouts' ? 'border-pitch-700 text-pitch-800' : 'border-transparent text-ink/50 hover:text-ink' }}">
            {{ __('Payouts') }}
        </a>
    @endhasanyrole
    @role('admin')
        <a href="{{ route('admin.staff.index') }}"
           class="px-4 py-2.5 text-sm font-medium border-b-2 -mb-px transition-colors {{ str_starts_with($active, 'admin.staff') ? 'border-pitch-700 text-pitch-800' : 'border-transparent text-ink/50 hover:text-ink' }}">
            {{ __('Stadium managers') }}
        </a>
    @endrole
</nav>
