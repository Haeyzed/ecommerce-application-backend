<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html dir="ltr" lang="en">
  <head>
    <link
      rel="preload"
      as="image"
      href="{{ $logoUrl ?? 'https://lawma.softalliance.com/assets/LAWMA-white2Asset-CGHAptko.png' }}"
    />
    <meta content="text/html; charset=UTF-8" http-equiv="Content-Type" />
    <meta name="x-apple-disable-message-reformatting" />
    <link
      href="https://api.fontshare.com/v2/css?f[]=satoshi@1&amp;display=swap"
      rel="stylesheet"
    />
  </head>
  <body style="background-color: #f6f9fc">
    <div
      style="
        display: none;
        overflow: hidden;
        line-height: 1px;
        opacity: 0;
        max-height: 0;
        max-width: 0;
      "
      data-skip-in-text="true"
    >
      {{ $subject ?? 'Notification' }}
    </div>
    <table
      border="0"
      width="100%"
      cellpadding="0"
      cellspacing="0"
      role="presentation"
      align="center"
    >
      <tbody>
        <tr>
          <td
            style="
              background-color: #f6f9fc;
              font-family:
                'Satoshi',
                -apple-system,
                BlinkMacSystemFont,
                'Segoe UI',
                Roboto,
                'Helvetica Neue',
                Ubuntu,
                sans-serif;
            "
          >
            <table
              align="center"
              width="100%"
              border="0"
              cellpadding="0"
              cellspacing="0"
              role="presentation"
              style="
                max-width: 37.5em;
                background-color: #ffffff;
                margin: 0 auto;
                padding: 0;
              "
            >
              <tbody>
                <tr style="width: 100%">
                  <td>
                    <table
                      align="center"
                      width="100%"
                      border="0"
                      cellpadding="0"
                      cellspacing="0"
                      role="presentation"
                      style="
                        padding: 10px 48px;
                        background-color: {{ $headerBgColor ?? '#1e2b2e' }};
                        text-align: center;
                      "
                    >
                      <tbody>
                        <tr>
                          <td>
                            <img
                              alt="{{ $logoAlt ?? 'Logo' }}"
                              height="50"
                              src="{{ $logoUrl ?? 'https://lawma.softalliance.com/assets/LAWMA-white2Asset-CGHAptko.png' }}"
                              style="
                                display: block;
                                outline: none;
                                border: none;
                                text-decoration: none;
                              "
                            />
                          </td>
                        </tr>
                      </tbody>
                    </table>
                    <table
                      align="center"
                      width="100%"
                      border="0"
                      cellpadding="0"
                      cellspacing="0"
                      role="presentation"
                      style="
                        padding: 40px 48px;
                        border-bottom: 5px solid {{ $accentColor ?? '#73bc1c' }};
                      "
                    >
                      <tbody>
                        <tr>
                          <td>
                            <p
                              style="
                                font-size: 16px;
                                line-height: 24px;
                                font-weight: 800;
                                color: #070d09;
                                margin-bottom: 8px;
                                margin-top: 16px;
                              "
                            >
                              {{ $greeting ?? 'Hello,' }}
                            </p>
                            <div
                              style="
                                font-size: 15px;
                                line-height: 24px;
                                color: #525f7f;
                                margin-bottom: 16px;
                                text-align: left;
                                margin-top: 16px;
                              "
                            >
                              {!! $body !!}
                            </div>
                            <p
                              style="
                                font-size: 15px;
                                line-height: 24px;
                                color: #525f7f;
                                margin-bottom: 16px;
                                text-align: left;
                                margin-top: 16px;
                              "
                            >
                              {{ $closing ?? 'Best regards,' }}<br />{{ $signOff ?? config('app.name') }}.
                            </p>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </td>
                </tr>
              </tbody>
            </table>
          </td>
        </tr>
      </tbody>
    </table>
  </body>
</html>
