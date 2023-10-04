<?php

namespace App\Http\Controllers\Web;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Crypt;
use App\Http\Controllers\Controller;
use App\Models\Web\CountryName;
use App\Models\Web\LinkReport;
use App\Models\Web\LinksTester;
use GuzzleHttp\Client;
use GuzzleHttp\TransferStats;
use Illuminate\Http\Request;
use Illuminate\Process\Exceptions\ProcessFailedException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;

class LinkRedirectionCheckController extends Controller
{
    // home page of linkTester
    public function dashboard(Request $request, $unique_id = '')
    {
        $inputUrl = '';
        $report = [];
        if ($unique_id != '') {
            $inputUrl = LinksTester::where('unique_id', $unique_id)->first();
            $report = LinkReport::where('url_id', $inputUrl->id)->first();
        }

        // dd($report);
        $countriesName = CountryName::get(['country']);
        return view('Web.pages.home.home', [
            'countriesName' => $countriesName,
            'report' => $report,
            'inputUrl' => $inputUrl
        ]);
    }

    // display details after share link text
    public function show(Request $request, $unique_id)
    {
        $inputUrl = LinksTester::where('unique_id', $unique_id)->first();
        $report = LinkReport::where('url_id', $inputUrl->id)->first();
        // dd($report);
        return view('Web.pages.home.shareResult', [
            'report' => $report,
            'inputUrl' => $inputUrl
        ]);
    }



    // Store data in link_tester table
    public function store(Request $request)
    {
        // Get ip address 
        $ipAddress = $request->ip();

        $validated = $request->validate([
            'url' =>  "url",
            'device' =>  "required",
            'country' =>  "required",
        ]);
        $tester = new LinksTester();
        $tester->url = $request->input('url');
        $tester->country = $request->input('country');
        $tester->device = $request->input('device');
        $tester->ip_address =  $ipAddress;
        $tester->user_agent = $request->input('user_agent', 0);
        // Make id to encrypted
        $tester->unique_id = sha1(md5($tester->id . now()));
        $tester->save();


        $url = $tester->url;
        $id = $tester->id;
        $redirection_chain = $this->checkLink($request, $id);

        return [
            'success' => true,
            "redirection_chain" => $redirection_chain,
            "tester" => $tester,
        ];
    }

    // link tester functionality
    public function checkLink(Request $request, $id)
    {
        // Define User-Agent  for different devices
        $userAgentMappings = [
            // Android
            'android 7' => 'Mozilla/5.0 (Linux; Android 7.0; Pixel C Build/NRD90M; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/52.0.2743.98 Safari/537.36',

            'android 9' => 'Mozilla/5.0 (Linux; Android 9; AFTWMST22 Build/PS7233; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/88.0.4324.152 Mobile Safari/537.36',

            'android 10' => 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Mobile Safari/537.36',

            'android 13' => 'Mozilla/5.0 (Linux; Android 13; SM-S901B) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/112.0.0.0 Mobile Safari/537.36',

            'desktop' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3',
            // iphone
            'ios 11' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 11_0 like Mac OS X) AppleWebKit/604.1.38 (KHTML, like Gecko) Version/11.0 Mobile/15A372 Safari/604.1',

            'ios 12' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 12_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) FxiOS/13.2b11866 Mobile/16A366 Safari/605.1.15',

            'ios 13' => 'Mozilla/5.0 (iPhone12,1; U; CPU iPhone OS 13_0 like Mac OS X) AppleWebKit/602.1.50 (KHTML, like Gecko) Version/10.0 Mobile/15E148 Safari/602.1',
        ];
        $selectedDevice = $request->input('device');

        if (array_key_exists($selectedDevice, $userAgentMappings)) {
            $userAgent = $userAgentMappings[$selectedDevice];
        } else {
            // when devise not selected in that case default user agent send
            $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3';
        }
        // dd($userAgent);
        $client = new Client([
            'headers' => [
                'User-Agent' => $userAgent,
                'CURLOPT_ENCODING' => 'gzip, deflate',
                'CURLOPT_HTTPHEADER' => array(
                    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                    'Accept-Language: en-US,en;q=0.5',
                    'Accept-Encoding: gzip, deflate',
                    'Connection: keep-alive',
                    'Upgrade-Insecure-Requests: 1',
                ),

            ],
            // 'proxy' => 'https://185.105.102.179:80',

            'curl' => [
                CURLOPT_SSL_VERIFYPEER => false
            ],

            'allow_redirects' => ['track_redirects' => true],
            'cookies' => true,
        ]);
        $redirection_chain = [];


        $url =  $request->input('url');
        while ($url) {
            $response = $client->request('GET', $url, ['allow_redirects' => false]);
            // dd($response->getHeaders());
            $statusCode = $response->getStatusCode();
            if ($statusCode >= 300 && $statusCode < 400) {

                $location = $response->getHeader('Location')[0];
                $redirection_chain[] = $location;
                $url = $location;
            } else {

                break;
            }
        }
        $this->linkReport($request, $statusCode, $id, $redirection_chain);
        // dd($redirection_chain);
        return $redirection_chain;
    }

    // Store data in link_report table
    public function linkReport(Request $request, $statusCode, $id, $redirection_chain)
    {
        $report = new LinkReport();
        $report->url_id = $id;
        $report->status_code = $statusCode;
        $report->response = json_encode($redirection_chain);
        $report->save();
    }
}
