@extends('layouts.admin')

@section('title', 'Site Settings')

@push('styles')
    <style>
        .settings-tabs .nav-link {
            font-weight: 500;
        }

        .settings-tabs .nav-link.active {
            color: #1a1a1a;
            border-color: #dee2e6 #dee2e6 #fff;
        }

        .media-preview-box {
            max-width: 160px;
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }

        .favicon-preview {
            width: 32px;
            height: 32px;
            object-fit: contain;
        }

        .theme-color-row {
            align-items: end;
        }

        .theme-color-picker {
            width: 3rem;
            height: 2.5rem;
            padding: 0.2rem;
            cursor: pointer;
        }

        .theme-preview {
            border: 1px solid #dee2e6;
            border-radius: 10px;
            padding: 1.25rem;
            background: #fff;
        }

        .theme-preview__strip {
            color: #fff;
            font-size: 0.75rem;
            font-weight: 500;
            text-align: center;
            padding: 0.5rem;
            border-radius: 6px;
            margin-bottom: 1rem;
        }

        .theme-preview__btn {
            display: inline-block;
            color: #fff;
            font-size: 0.875rem;
            font-weight: 600;
            padding: 0.5rem 1.25rem;
            border-radius: 8px;
            border: none;
        }

        .theme-preview__link {
            font-size: 0.875rem;
            font-weight: 500;
            margin-left: 1rem;
        }

        .theme-preview__hero {
            margin-top: 1rem;
            padding: 1rem;
            border-radius: 8px;
            font-size: 0.8125rem;
            color: #3d3d3d;
        }

        .admin-settings-preview__swatch {
            width: 2rem;
            height: 2rem;
            border-radius: 8px;
            border: 1px solid rgba(0, 0, 0, 0.08);
        }
    </style>
@endpush

@php
    $themePrimary = old('theme_primary', $settings->theme_primary ?? config('store.theme.primary'));
    $themePrimaryDark = old('theme_primary_dark', $settings->theme_primary_dark ?? config('store.theme.primary_dark'));
    $themeHeroAccent = old('theme_hero_accent', $settings->theme_hero_accent ?? config('store.theme.hero_accent'));
    $adminTheme = old('admin_theme', $settings->admin_theme ?? config('store.admin.theme', 'light'));
    $adminColorPrimary = old(
        'admin_color_primary',
        $settings->admin_color_primary ?? config('store.admin.colors.primary'),
    );
    $adminColorSecondary = old(
        'admin_color_secondary',
        $settings->admin_color_secondary ?? config('store.admin.colors.secondary'),
    );
    $adminColorNeutral = old(
        'admin_color_neutral',
        $settings->admin_color_neutral ?? config('store.admin.colors.neutral'),
    );
@endphp

@section('content')
    <h1 class="h3 mb-4">Site Settings</h1>

    <div class="admin-card">
        <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" data-rich-text>
            @csrf
            @method('PUT')

            <ul class="nav nav-tabs settings-tabs mb-4" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="tab-general" data-bs-toggle="tab" data-bs-target="#pane-general"
                        type="button" role="tab">General</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-appearance" data-bs-toggle="tab" data-bs-target="#pane-appearance"
                        type="button" role="tab">Appearance</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-contact" data-bs-toggle="tab" data-bs-target="#pane-contact"
                        type="button" role="tab">Contact &amp; Hours</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-social" data-bs-toggle="tab" data-bs-target="#pane-social"
                        type="button" role="tab">Social</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-legal" data-bs-toggle="tab" data-bs-target="#pane-legal" type="button"
                        role="tab">Legal Pages</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-seo" data-bs-toggle="tab" data-bs-target="#pane-seo" type="button"
                        role="tab">SEO Defaults</button>
                </li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane fade show active" id="pane-general" role="tabpanel">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Site name</label>
                            <input type="text" name="site_name" class="form-control"
                                value="{{ old('site_name', $settings->site_name) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Brand suffix</label>
                            <input type="text" name="brand_suffix" class="form-control"
                                value="{{ old('brand_suffix', $settings->brand_suffix) }}" placeholder="Foods">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tagline</label>
                            <input type="text" name="tagline" class="form-control"
                                value="{{ old('tagline', $settings->tagline) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Header promo (primary)</label>
                            <input type="text" name="promo_primary" class="form-control"
                                value="{{ old('promo_primary', $settings->promo_primary) }}"
                                placeholder="Free shipping on orders above Rs. 2,000">
                            <div class="form-text">Top strip, left side. Leave empty to hide the strip if secondary is also
                                empty.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Header promo (secondary)</label>
                            <input type="text" name="promo_secondary" class="form-control"
                                value="{{ old('promo_secondary', $settings->promo_secondary) }}"
                                placeholder="Handmade in Thimi">
                            <div class="form-text">Top strip, right side (shown after | separator).</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Footer description</label>
                            <textarea name="footer_description" class="form-control" rows="3">{{ old('footer_description', $settings->footer_description) }}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Copyright text</label>
                            <input type="text" name="copyright_text" class="form-control"
                                value="{{ old('copyright_text', $settings->copyright_text) }}"
                                placeholder="Leave blank for auto-generated year + site name">
                            <div class="form-text">HTML allowed for &copy; symbol, e.g. <code>&amp;copy; 2026 Our
                                    site</code></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Logo</label>
                            <input type="file" name="logo" class="form-control" accept="image/*">
                            <div class="form-text">Horizontal wordmark recommended: 480&times;96&nbsp;px (or similar
                                4:1&ndash;5:1 ratio), PNG/SVG with transparent background. Displays up to 56px tall in
                                the header.</div>
                            @if ($settings->getFirstMediaUrl('logo'))
                                <img src="{{ $settings->getFirstMediaUrl('logo', 'header') ?: $settings->getFirstMediaUrl('logo') }}"
                                    alt="Logo" class="media-preview-box mt-2">
                            @endif
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Favicon</label>
                            <input type="file" name="favicon" class="form-control"
                                accept="image/png,image/x-icon,image/vnd.microsoft.icon,image/jpeg,image/webp">
                            @if ($settings->getFirstMediaUrl('favicon'))
                                <img src="{{ $settings->getFirstMediaUrl('favicon') }}" alt="Favicon"
                                    class="favicon-preview mt-2">
                            @endif
                            <div class="form-text">Square image, max 512×512 px.</div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="pane-appearance" role="tabpanel">
                    <p class="text-muted small mb-3">
                        Controls storefront buttons, links, header strip, and hero accents. Changes apply after you save
                        (cached for up to 1 hour).
                    </p>
                    <div class="row g-4">
                        <div class="col-lg-7">
                            @foreach ([
            'theme_primary' => ['label' => 'Primary brand color', 'hint' => 'Buttons, links, top promo strip', 'value' => $themePrimary],
            'theme_primary_dark' => ['label' => 'Primary dark (hover)', 'hint' => 'Hover states and gradients', 'value' => $themePrimaryDark],
            'theme_hero_accent' => ['label' => 'Hero accent background', 'hint' => 'Soft background on hero sections', 'value' => $themeHeroAccent],
        ] as $field => $meta)
                                <div class="row g-2 theme-color-row mb-3" data-theme-field="{{ $field }}">
                                    <div class="col-md-5">
                                        <label class="form-label">{{ $meta['label'] }}</label>
                                        <div class="form-text">{{ $meta['hint'] }}</div>
                                    </div>
                                    <div class="col-auto">
                                        <input type="color" class="form-control form-control-color theme-color-picker"
                                            value="{{ $meta['value'] }}" data-theme-picker="{{ $field }}"
                                            aria-label="{{ $meta['label'] }} picker">
                                    </div>
                                    <div class="col">
                                        <input type="text" name="{{ $field }}"
                                            class="form-control font-monospace" value="{{ $meta['value'] }}"
                                            maxlength="7" pattern="^#[0-9A-Fa-f]{6}$" placeholder="#b91c1c"
                                            data-theme-hex="{{ $field }}">
                                        @error($field)
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            @endforeach

                            <hr class="my-4">

                            <h3 class="h6 mb-2">Admin panel</h3>
                            <p class="text-muted small mb-3">
                                Colors and light/dark mode for the CMS. Storefront colors above are separate.
                            </p>

                            @foreach ([
            'admin_color_primary' => ['label' => 'Primary', 'hint' => 'Buttons, active nav, headings', 'value' => $adminColorPrimary],
            'admin_color_secondary' => ['label' => 'Secondary', 'hint' => 'Accents, icons, highlights', 'value' => $adminColorSecondary],
            'admin_color_neutral' => ['label' => 'Neutral', 'hint' => 'Muted text and borders', 'value' => $adminColorNeutral],
        ] as $field => $meta)
                                <div class="row g-2 theme-color-row mb-3" data-admin-color-field="{{ $field }}">
                                    <div class="col-md-5">
                                        <label class="form-label">{{ $meta['label'] }}</label>
                                        <div class="form-text">{{ $meta['hint'] }}</div>
                                    </div>
                                    <div class="col-auto">
                                        <input type="color" class="form-control form-control-color theme-color-picker"
                                            value="{{ $meta['value'] }}" data-admin-color-picker="{{ $field }}"
                                            aria-label="{{ $meta['label'] }} picker">
                                    </div>
                                    <div class="col">
                                        <input type="text" name="{{ $field }}"
                                            class="form-control font-monospace" value="{{ $meta['value'] }}"
                                            maxlength="7" pattern="^#[0-9A-Fa-f]{6}$"
                                            data-admin-color-hex="{{ $field }}">
                                        @error($field)
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            @endforeach

                            <div class="mb-3">
                                <label class="form-label">Admin preview</label>
                                <div class="admin-settings-preview p-3 rounded border" id="adminColorPreview">
                                    <div class="d-flex gap-2 mb-2">
                                        <span class="admin-settings-preview__swatch" data-admin-preview="primary"></span>
                                        <span class="admin-settings-preview__swatch"
                                            data-admin-preview="secondary"></span>
                                        <span class="admin-settings-preview__swatch" data-admin-preview="neutral"></span>
                                    </div>
                                    <button type="button" class="btn btn-sm text-white" data-admin-preview-btn>Sign
                                        in</button>
                                    <span class="small ms-2" data-admin-preview-muted>Muted label text</span>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="admin_theme">Default admin theme</label>
                                <select name="admin_theme" id="admin_theme" class="form-select"
                                    style="max-width: 16rem;">
                                    <option value="light" @selected($adminTheme === 'light')>Light</option>
                                    <option value="dark" @selected($adminTheme === 'dark')>Dark</option>
                                    <option value="system" @selected($adminTheme === 'system')>System (match device)</option>
                                </select>
                                @error('admin_theme')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    Env: <code>ADMIN_THEME</code>, <code>ADMIN_COLOR_PRIMARY</code>,
                                    <code>ADMIN_COLOR_SECONDARY</code>, <code>ADMIN_COLOR_NEUTRAL</code>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-5">
                            <label class="form-label">Storefront preview</label>
                            <div class="theme-preview" id="themePreview">
                                <div class="theme-preview__strip" id="previewStrip">Free shipping on orders above Rs.
                                    2,000</div>
                                <button type="button" class="theme-preview__btn" id="previewBtn">Add to cart</button>
                                <a href="#" class="theme-preview__link" id="previewLink"
                                    onclick="return false;">View details</a>
                                <div class="theme-preview__hero" id="previewHero">Hero section accent background</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="pane-contact" role="tabpanel">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Contact email</label>
                            <input type="email" name="contact_email" class="form-control"
                                value="{{ old('contact_email', $settings->contact_email) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Contact phone</label>
                            <input type="text" name="contact_phone" class="form-control"
                                value="{{ old('contact_phone', $settings->contact_phone) }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Address</label>
                            <textarea name="contact_address" class="form-control" rows="2">{{ old('contact_address', $settings->contact_address) }}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Business hours (display text)</label>
                            <input type="text" name="hours_display_text" class="form-control"
                                value="{{ old('hours_display_text', $settings->hours_display_text) }}"
                                placeholder="Sun–Fri, 10:00 AM – 6:00 PM">
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="pane-social" role="tabpanel">
                    <div class="row g-3">
                        @foreach ([
            'facebook_url' => 'Facebook URL',
            'instagram_url' => 'Instagram URL',
            'youtube_url' => 'YouTube URL',
            'tiktok_url' => 'TikTok URL',
        ] as $field => $label)
                            <div class="col-md-6">
                                <label class="form-label">{{ $label }}</label>
                                <input type="url" name="{{ $field }}" class="form-control"
                                    value="{{ old($field, $settings->{$field}) }}" placeholder="https://">
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="tab-pane fade" id="pane-legal" role="tabpanel">
                    <p class="text-muted small mb-3">
                        Content appears on the public policy pages. Saving updates the &ldquo;Last updated&rdquo; date
                        automatically.
                    </p>
                    <div class="mb-4">
                        <label class="form-label d-flex justify-content-between">
                            <span>Privacy Policy</span>
                            @if ($settings->privacy_updated_at)
                                <span class="text-muted small">Last updated:
                                    {{ $settings->privacy_updated_at->format('M j, Y') }}</span>
                            @endif
                        </label>
                        <textarea name="privacy_policy" class="form-control rich-text" rows="12">{{ old('privacy_policy', $settings->privacy_policy) }}</textarea>
                    </div>
                    <div class="mb-4">
                        <label class="form-label d-flex justify-content-between">
                            <span>Terms &amp; Conditions</span>
                            @if ($settings->terms_updated_at)
                                <span class="text-muted small">Last updated:
                                    {{ $settings->terms_updated_at->format('M j, Y') }}</span>
                            @endif
                        </label>
                        <textarea name="terms_conditions" class="form-control rich-text" rows="12">{{ old('terms_conditions', $settings->terms_conditions) }}</textarea>
                    </div>
                    <div class="mb-4">
                        <label class="form-label d-flex justify-content-between">
                            <span>Refund Policy</span>
                            @if ($settings->refund_updated_at)
                                <span class="text-muted small">Last updated:
                                    {{ $settings->refund_updated_at->format('M j, Y') }}</span>
                            @endif
                        </label>
                        <textarea name="refund_policy" class="form-control rich-text" rows="12">{{ old('refund_policy', $settings->refund_policy) }}</textarea>
                    </div>
                </div>

                <div class="tab-pane fade" id="pane-seo" role="tabpanel">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Default meta title</label>
                            <input type="text" name="default_meta_title" class="form-control"
                                value="{{ old('default_meta_title', $settings->default_meta_title) }}"
                                placeholder="JheeKuma Clay Arts | Handmade Pottery from Thimi, Nepal">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Default meta description</label>
                            <textarea name="default_meta_description" class="form-control" rows="3">{{ old('default_meta_description', $settings->default_meta_description) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4 pt-3 border-top">
                <button type="submit" class="btn btn-dark">Save settings</button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/tinymce@7/tinymce.min.js"></script>
    <script src="{{ asset('js/admin-rich-text.js') }}"></script>
    <script>
        (function() {
            const fields = ['theme_primary', 'theme_primary_dark', 'theme_hero_accent'];
            const strip = document.getElementById('previewStrip');
            const btn = document.getElementById('previewBtn');
            const link = document.getElementById('previewLink');
            const hero = document.getElementById('previewHero');

            function normalizeHex(value) {
                let v = (value || '').trim();
                if (!v) return '';
                if (!v.startsWith('#')) v = '#' + v;
                if (!/^#[0-9A-Fa-f]{6}$/.test(v)) return '';
                return v.toUpperCase();
            }

            function read(field) {
                const hex = document.querySelector('[data-theme-hex="' + field + '"]');
                return normalizeHex(hex ? hex.value : '');
            }

            function syncPicker(field, hex) {
                const picker = document.querySelector('[data-theme-picker="' + field + '"]');
                if (picker && hex) picker.value = hex;
            }

            function updatePreview() {
                const primary = read('theme_primary') || '#b91c1c';
                const primaryDark = read('theme_primary_dark') || '#991b1b';
                const heroAccent = read('theme_hero_accent') || '#dceee9';
                if (strip) strip.style.background = primary;
                if (btn) {
                    btn.style.background = primary;
                    btn.onmouseenter = () => {
                        btn.style.background = primaryDark;
                    };
                    btn.onmouseleave = () => {
                        btn.style.background = primary;
                    };
                }
                if (link) link.style.color = primary;
                if (hero) hero.style.background = heroAccent;
            }

            fields.forEach((field) => {
                const picker = document.querySelector('[data-theme-picker="' + field + '"]');
                const hex = document.querySelector('[data-theme-hex="' + field + '"]');
                if (!picker || !hex) return;

                picker.addEventListener('input', () => {
                    hex.value = picker.value.toUpperCase();
                    updatePreview();
                });

                hex.addEventListener('input', () => {
                    const normalized = normalizeHex(hex.value);
                    if (normalized) syncPicker(field, normalized);
                    updatePreview();
                });

                hex.addEventListener('blur', () => {
                    const normalized = normalizeHex(hex.value);
                    if (normalized) hex.value = normalized;
                    updatePreview();
                });
            });

            updatePreview();

            const adminFields = ['admin_color_primary', 'admin_color_secondary', 'admin_color_neutral'];
            const adminPreviewBtn = document.querySelector('[data-admin-preview-btn]');
            const adminPreviewMuted = document.querySelector('[data-admin-preview-muted]');

            function readAdmin(field) {
                const hex = document.querySelector('[data-admin-color-hex="' + field + '"]');
                return normalizeHex(hex ? hex.value : '');
            }

            function updateAdminPreview() {
                const primary = readAdmin('admin_color_primary') || '#3D2914';
                const secondary = readAdmin('admin_color_secondary') || '#C9A227';
                const neutral = readAdmin('admin_color_neutral') || '#7A6B5C';

                document.querySelectorAll('[data-admin-preview]').forEach((el) => {
                    const key = el.getAttribute('data-admin-preview');
                    if (key === 'primary') el.style.background = primary;
                    if (key === 'secondary') el.style.background = secondary;
                    if (key === 'neutral') el.style.background = neutral;
                });

                if (adminPreviewBtn) adminPreviewBtn.style.background = primary;
                if (adminPreviewMuted) adminPreviewMuted.style.color = neutral;
            }

            adminFields.forEach((field) => {
                const picker = document.querySelector('[data-admin-color-picker="' + field + '"]');
                const hex = document.querySelector('[data-admin-color-hex="' + field + '"]');
                if (!picker || !hex) return;

                picker.addEventListener('input', () => {
                    hex.value = picker.value.toUpperCase();
                    updateAdminPreview();
                });

                hex.addEventListener('input', () => {
                    const normalized = normalizeHex(hex.value);
                    if (normalized) picker.value = normalized;
                    updateAdminPreview();
                });

                hex.addEventListener('blur', () => {
                    const normalized = normalizeHex(hex.value);
                    if (normalized) hex.value = normalized;
                    updateAdminPreview();
                });
            });

            updateAdminPreview();
        })();
    </script>
@endpush
