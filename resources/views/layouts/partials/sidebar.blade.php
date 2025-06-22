<div class="list-group">
    <a href="{{ route('dashboard') }}" class="list-group-item list-group-item-action {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        Dashboard
    </a>

    @role('admin')
    <a href="{{ url('/type-of-works/view') }}" class="list-group-item list-group-item-action {{ request()->is('type-of-works*') ? 'active' : '' }}">
        Type of Works
    </a>
    @endrole

    @role('admin')
    <a href="{{ url('/contractors/view') }}" class="list-group-item list-group-item-action {{ request()->is('contractors*') ? 'active' : '' }}">
        Contractors
    </a>
    @endrole

    @role('admin')
    <a href="{{ url('/conductors/view') }}" class="list-group-item list-group-item-action {{ request()->is('conductors*') ? 'active' : '' }}">
        Conductors
    </a>
    @endrole

    @role('admin')
    <a href="{{ url('/job-orders/view') }}" class="list-group-item list-group-item-action {{ request()->is('job-orders*') ? 'active' : '' }}">
        Job Orders
    </a>
    @endrole

    @role('admin')
    <a href="{{ url('/jos/view') }}" class="list-group-item list-group-item-action {{ request()->is('jos*') ? 'active' : '' }}">
        Job Order Statements (JOS)
    </a>
    @endrole
    
</div>
