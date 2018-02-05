<b-navbar toggleable="md" variant="light" ref="navbar" class="mb-3">
	<b-navbar-toggle target="navbar-collapse2"></b-navbar-toggle>
	<b-container>
		<b-navbar-brand href="{{ route('admin.home') }}">{{ config('app.name') }} - Amministrazione</b-navbar-brand>
		<b-collapse is-nav id="navbar-collapse2">
			<b-navbar-nav>
				<b-nav-item-dropdown text="Esercizi">
					<b-dropdown-item href="{{ route('admin.venues.index') }}">Esercizi attivi</b-dropdown-item>
					<b-dropdown-item href="{{ route('admin.venues.obsolete.index') }}">Esercizi obsoleti</b-dropdown-item>
					<b-dropdown-item href="{{ route('admin.venues.unmanaged.index') }}">Esercizi da gestire</b-dropdown-item>
					<b-dropdown-divider></b-dropdown-divider>
					<b-dropdown-item href="{{ route('admin.venues.import.edit') }}">Carica file CSV</b-dropdown-item>
				</b-nav-item-dropdown>
				<b-nav-item href="{{ route('admin.users.index') }}">Utenti</b-nav-item>
			</b-navbar-nav>
			<b-navbar-nav class="ml-auto">
				<b-nav-text><strong>{{ Auth::user()->name }}</strong></b-nav-text>
				<b-nav-item href="{{ url('/logout') }}" @click.prevent="onLogoutClick">Esci</b-nav-item>
				<form action="{{ url('/logout') }}" method="post" hidden ref="logoutForm">
					{{ csrf_field() }}
				</form>
			</b-navbar-nav>
		</b-collapse>
	</b-container>
</b-navbar>