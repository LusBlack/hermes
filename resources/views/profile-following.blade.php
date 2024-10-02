<x-profile :sharedData="$sharedData" doctitle="Who {{$sharedData['username']}} Follows">
@include(view('profile-following-only'))
</x-profile>
