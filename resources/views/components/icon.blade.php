@props(['name'])

{{--
This component acts as a safe wrapper.
It uses the @svg directive from the blade-ui-kit/blade-icons package,
which is a dependency of TableNice. This ensures icons are always
rendered correctly without conflicting with the host application's components.
The $attributes variable is automatically passed and merged by Laravel.
--}}
@svg($name, $attributes)